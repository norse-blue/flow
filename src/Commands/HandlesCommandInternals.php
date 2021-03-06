<?php

namespace NorseBlue\Flow\Commands;

use NorseBlue\Flow\Commands\Arguments\ArgumentsCollection;
use NorseBlue\Flow\Commands\Arguments\HandlesArguments;
use NorseBlue\Flow\Commands\Options\HandlesOptions;
use NorseBlue\Flow\Commands\Options\OptionsCollection;
use NorseBlue\Flow\FluidCommand;

/**
 * Trait HandlesCommandInternals
 *
 * @package NorseBlue\Flow
 */
trait HandlesCommandInternals
{
    use HandlesArguments;
    use HandlesOptions;

    /** @var string The command name */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function init(array $arguments_definition, array $options_definition): void
    {
        $this->arguments = ArgumentsCollection::create($arguments_definition);
        $this->options = OptionsCollection::create($options_definition);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(): ArgumentsCollection
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): OptionsCollection
    {
        return $this->options;
    }

    /**
     * Check if the given call name is for an option.
     *
     * @param string $name The call name.
     *
     * @return bool
     */
    protected function isCallForOption(string $name): bool
    {
        return strpos($name, '_') === 0;
    }

    /**
     * Magic method to get arguments or options values.
     *
     * @param string $name The name of the argument or option.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($this->isCallForOption($name)) {
            return $this->getOptions()->get($this->methodToOptionName($name));
        }

        return $this->getArguments()->get($name);
    }

    /**
     * Magic method to set arguments or options values.
     *
     * @param string $name  The argument or option name.
     * @param mixed  $value The argument or option value.
     *
     * @return self
     */
    public function __set(string $name, $value): self
    {
        $this->{$name}($value);

        return $this;
    }

    /**
     * Magic method to check if argument or option is set.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        if ($this->isCallForOption($name)) {
            return $this->getOptions()->isset($this->methodToOptionName($name));
        }

        return $this->getArguments()->isset($name);
    }

    /**
     * Magic method to translate function calls to argument or options.
     *
     * @param string $name      The name of the argument or option.
     * @param array  $arguments The arguments of the call.
     *
     * @return self
     */
    public function __call(string $name, array $arguments): self
    {
        if ($this->isCallForOption($name)) {
            $this->getOptions()->set($this->methodToOptionName($name), $arguments[0]);
            return $this;
        }

        $this->getArguments()->set($name, $arguments[0]);

        return $this;
    }

    /**
     * Gets the command as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        $str = $this->name;

        $options = (string)$this->options;
        if (!empty($options)) {
            $str .= sprintf(' %s', $options);
        }

        $arguments = (string)$this->arguments;
        if (!empty($arguments)) {
            $str .= sprintf(' %s', $arguments);
        }

        return $str;
    }
}
