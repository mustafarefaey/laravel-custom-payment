<?php

namespace MustafaRefaey\LaravelCustomPayment\Tests\Commands;

use MustafaRefaey\LaravelCustomPayment\Tests\TestCase;

class AddPaymentActionCommandTest extends TestCase
{
    /** @test */
    public function add_payment_action_command_exists()
    {
        $this->artisan("payment:add-action")->assertExitCode(0);
    }

    /** @test */
    public function add_payment_action_command_should_add_an_entry_in_config()
    {
        $name = 'some_payment_action';
        $class = 'SomePaymentAction';
        $this->artisan("payment:add-action", ['--name' => $name, '--class' => $class])->assertExitCode(0);

        $paymentConfig = require config_path('laravel-custom-payment.php');
        $actionsNamespace = $paymentConfig['actions_namespace'];
        $this->assertArrayHasKey($name, $paymentConfig['actions']);
        $this->assertEquals($actionsNamespace . "\\" . $class, $paymentConfig['actions'][$name]);
    }

    /** @test */
    public function add_payment_action_command_should_scaffold_a_class_in_actions_directory()
    {
        $name = 'some_payment_action';
        $class = 'SomePaymentAction';
        $this->artisan("payment:add-action", ['--name' => $name, '--class' => $class])->assertExitCode(0);

        $paymentConfig = require config_path('laravel-custom-payment.php');
        $this->assertFileExists(base_path($paymentConfig['actions_directory'] . '/' . $class . '.php'));
    }

    /** @test */
    public function add_payment_action_command_should_scaffold_a_class_implements_actions_interface()
    {
        $name = 'some_payment_action';
        $class = 'SomePaymentAction';
        $this->artisan("payment:add-action", ['--name' => $name, '--class' => $class])->assertExitCode(0);

        $paymentConfig = require config_path('laravel-custom-payment.php');
        $actionClassPath = base_path($paymentConfig['actions_directory'] . '/' . $class . '.php');
        $this->assertFileExists($actionClassPath);

        require $actionClassPath;
        $fullClass = '\\' . $paymentConfig['actions_namespace'] . '\\' . $class;
        $action = new $fullClass();

        $this->assertEquals('MustafaRefaey\\LaravelCustomPayment\\PaymentAction', get_parent_class($action));
    }
}
