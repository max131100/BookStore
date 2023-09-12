<?php

namespace App\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

#[Attribute(Attribute::TARGET_CLASS)]
class AtLeastOneRequired extends Constraint
{
    /**
     * @var string[]
     */
    public array $requiredFields;

    public string $message = 'At least one of {{ fields }} required.';

    public const ONE_REQUIRED_ERROR = '5ced255a-2e2d-4fc3-b1b0-115ef5a2d64d';

    protected static $errorNames = [
        self::ONE_REQUIRED_ERROR => 'ONE_REQUIRED_ERROR'
    ];

    public function __construct(
        array $options = [],
        array $requiredFields = null,
        string $message = null,
        array $groups = null,
        $payload = null)
    {
        if (!empty($options) && array_is_list($options)) {
            $requiredFields = $requiredFields ?? $options;
            $options = [];
        }

        if (empty($requiredFields)) {
            throw new ConstraintDefinitionException('The requiredFields of AtLeastOneRequired can not be empty');
        }

        $options['value'] = $requiredFields;

        parent::__construct($options, $groups, $payload);

        $this->requiredFields = $requiredFields;
        $this->message = $message ?? $this->message;
    }

    public function getRequiredOptions(): array
    {
        return ['requiredFields'];
    }

    public function getDefaultOption(): string
    {
        return 'requiredFields';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
