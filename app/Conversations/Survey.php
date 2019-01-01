<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class Survey extends Conversation
{
    protected $name;
    Protected $age;
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->say('Welcome to the survey');
        $this->ask('What is your name?', function ($answer) {
            $value = $answer->getText();

            if (trim($value) === '') {
                return $this->repeat('This name does not look real! What is your name?');
            } elseif (!preg_match("#^[a-zA-Z-'\s]+$#", $value)) {
                return $this->repeat('This name does not look real! What is your name?');
            }

            $this->name = $value;
            $this->say('Nice to meet you, '.$this->name);

            $this->askAge();
        });
    }

    private function askAge()
    {
        $this->ask('What is your age?', function ($answer) {
            $this->age = $answer->getText();
            $this->say('Your age is '.$this->age);
        });
    }
}
