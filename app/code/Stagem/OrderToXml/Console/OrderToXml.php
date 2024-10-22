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
    protected const ORDER_NUMBER_COMMAND = 'number';

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
                self::ORDER_NUMBER_COMMAND,
                null,
                InputOption::VALUE_REQUIRED,
                'Description: order number'
            )
        ];

        $this->setName('example:order')
             ->setDescription('Command line description')
             ->setDefinition($options);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputOrderNumber = $input->getOption(self::ORDER_NUMBER_COMMAND);

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
