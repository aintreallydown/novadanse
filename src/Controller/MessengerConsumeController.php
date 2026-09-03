<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessengerConsumeController extends AbstractController
{
    #[Route('/cron/messenger-consume/{token}', name: 'cron_messenger_consume', methods: ['GET'])]
    public function __invoke(string $token, KernelInterface $kernel): Response
    {
        $expectedToken = $this->getParameter('cron_secret_token');

        if (!hash_equals($expectedToken, $token)) {
            throw $this->createAccessDeniedException();
        }

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $output = new BufferedOutput();
        $application->run(new ArrayInput([
            'command' => 'messenger:consume',
            'receivers' => ['async'],
            '--limit' => 10,
            '--time-limit' => 50,
            '--no-interaction' => true,
        ]), $output);

        return new Response($output->fetch(), 200, ['Content-Type' => 'text/plain']);
    }
}
