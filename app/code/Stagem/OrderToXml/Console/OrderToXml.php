<?php

namespace Stagem\OrderToXml\Console;


use Stagem\OrderToXml\Model\DataRecipient;
use Stagem\OrderToXml\Service\GenerateXml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OrderToXml extends Command
{
    protected const COMMAND_NAME         = 'custom:order';
    protected const COMMAND_ORDER_NUMBER = 'number';
    protected const COMMAND_DESCRIPTION  = 'Receiving order data by order number';

    public function __construct(
        protected DataRecipient $dataRecipient,
        protected GenerateXml   $generateXml,
        ?string                 $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $options = [
            new InputOption(
                self::COMMAND_ORDER_NUMBER,
                null,
                InputOption::VALUE_REQUIRED,
                self::COMMAND_DESCRIPTION
            )
        ];

        $this->setName(self::COMMAND_NAME)
             ->setDescription('Command line description')
             ->setDefinition($options);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOrderNumber = $input->getOption(self::COMMAND_ORDER_NUMBER);

        if (isset($inputOrderNumber) === true) {
            $output->writeln("Order number: $inputOrderNumber");

            $data = $this->dataRecipient->getOrderInformation($inputOrderNumber);

            if ($data === null) {
                $output->writeln("Something went wrong") . PHP_EOL;
                return Command::FAILURE;
            }

            if ($this->generateXml->generateXml($data, $inputOrderNumber) === true) {
                $output->writeln("File $inputOrderNumber.xml created");
            }

            return Command::SUCCESS;
        }

        $output->writeln("This order number is empty");

        return Command::INVALID;
    }
}
