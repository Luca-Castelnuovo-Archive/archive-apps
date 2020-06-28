<?php

namespace App\Commands;

use Exception;
use CQ\JWT\Utils;
use CQ\Helpers\Str;
use CQ\Helpers\App as AppHelper;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class App
{
    /**
     * Generate key command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param SymfonyStyle $io
     *
     * @return void
     */
    public function key(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (AppHelper::environment('production')) {
            $io->note('Application In Production!');
            if (!$io->confirm('Do you really wish to run this command?', false)) {
                $io->note('Command Canceled!');

                return;
            }
        }

        try {
            $length = $io->ask('Key length', 64, function ($number) {
                if (!is_numeric($number)) {
                    throw new \RuntimeException('You must type a number.');
                }

                return (int) $number;
            });

            $key = Str::random($length);
            $path = __DIR__ . '/../../.env';

            if (!file_exists($path)) {
                $io->warning('.env file not found, please set key manually');
                $io->text("APP_KEY=\"{$key}\"");

                return;
            }

            file_put_contents($path, str_replace(
                'APP_KEY="' . getenv('APP_KEY') . '"',
                'APP_KEY="' . $key . '"',
                file_get_contents($path)
            ));
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return;
        }

        $io->success('Key set successfully');
    }

    /**
     * Generate JWT key command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param SymfonyStyle $io
     *
     * @return void
     */
    public function jwt(InputInterface $input, OutputInterface $output, SymfonyStyle $io)
    {
        if (AppHelper::environment('production')) {
            $io->note('Application In Production!');
            if (!$io->confirm('Do you really wish to run this command?', false)) {
                $io->note('Command Canceled!');

                return;
            }
        }

        try {
            $bits = $io->ask('Bit length', 2048, function ($number) {
                if (!is_numeric($number)) {
                    throw new \RuntimeException('You must type a number.');
                }

                if ($number > 8192) {
                    throw new \RuntimeException('Max keysize 8192 bits.');
                }

                return (int) $number;
            });

            $key = Utils::generateKeys($bits);
            $path = __DIR__ . '/../../.env';

            $private_key_string = str_replace(["\r", "\n"], '||', $key['privatekey']);
            $public_key_string = str_replace(["\r", "\n"], '||', $key['publickey']);

            if (!file_exists($path)) {
                $io->warning('.env file not found, please set key manually');
                $io->text("JWT_PRIVATE_KEY=\"{$private_key_string}\"");
                $io->text("JWT_PUBLIC_KEY=\"{$public_key_string}\"");

                return;
            }

            file_put_contents($path, str_replace(
                'JWT_PRIVATE_KEY="' . getenv('JWT_PRIVATE_KEY') . '"',
                'JWT_PRIVATE_KEY="' . $private_key_string . '"',
                file_get_contents($path)
            ));

            file_put_contents($path, str_replace(
                'JWT_PUBLIC_KEY="' . getenv('JWT_PUBLIC_KEY') . '"',
                'JWT_PUBLIC_KEY="' . $public_key_string . '"',
                file_get_contents($path)
            ));
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return;
        }

        $io->success('JWT keypair set successfully');
    }
}
