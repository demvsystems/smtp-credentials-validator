<?php

namespace Demv\SmtpValidator;

class PromptHelper
{

    /**
     * Source: https://www.sitepoint.com/interactive-cli-password-prompt-in-php/
     *
     * @param string $prompt
     *
     * @return string
     */
    public static function promptSilent(string $prompt = 'Enter Password: '): string
    {
        if (preg_match('/^win/i', PHP_OS)) {
            $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
            file_put_contents(
                $vbscript, 'wscript.echo(InputBox("'
                           . addslashes($prompt)
                           . '", "", "password here"))');
            $command  = "cscript //nologo " . escapeshellarg($vbscript);
            $password = rtrim(shell_exec($command));
            unlink($vbscript);

            return $password;
        } else {
            $command = "/usr/bin/env bash -c 'echo OK'";
            if (rtrim(shell_exec($command)) !== 'OK') {
                trigger_error("Can't invoke bash");

                return '';
            }
            $command  = "/usr/bin/env bash -c 'read -s -p \""
                        . addslashes($prompt)
                        . "\" mypassword && echo \$mypassword'";
            $password = rtrim(shell_exec($command));
            echo "\n";

            return $password;
        }
    }

    /**
     * @param string $prompt
     *
     * @return string
     */
    public static function prompt(string $prompt): string
    {
        echo $prompt;
        $handle = fopen('php://stdin', 'r');

        return trim(fgets($handle));
    }
}