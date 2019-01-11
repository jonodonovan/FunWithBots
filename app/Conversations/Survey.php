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
            $this->name = $answer->getText();

            if (trim($this->name) === '') {
                return $this->repeat('This name does not look real! What is your name?');
            } elseif (!preg_match("#^[a-zA-Z-'\s]+$#", $this->name)) {
                return $this->repeat('This name does not look real! What is your name?');
            }

            $this->say('Nice to meet you, '.$this->name);

            $this->askAge();
        });
    }

    private function askAge()
    {
        $this->ask('What is your age?', function ($answer) {
            $this->age = $answer->getText();
            $this->say($this->name.', your age is '.$this->age.'. Thanks for taking the survey.');
        });
    }
}
