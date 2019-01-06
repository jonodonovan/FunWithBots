<?php

namespace App\Conversations;

use App\Lunch;
use BotMan\BotMan\Messages\Conversations\Conversation;

class LunchConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->say('Let\'s order lunch!');
        $this->ask('What is your name?', function ($answer) {
            $this->name = $answer->getText();

            if (trim($this->name) === '') {
                return $this->repeat('This name does not look real! What is your name?');
            } elseif (!preg_match("#^[a-zA-Z-'\s]+$#", $this->name)) {
                return $this->repeat('This name does not look real! What is your name?');
            }

            $this->say('Nice to meet you, '.$this->name);

            $this->askOrder();
        });
    }

    private function askOrder()
    {
        $this->ask('What would you like for lunch?', function ($answer) {
            $this->order = $answer->getText();
            $this->say('Okay, I have a '.$this->order.' for '.$this->name);
            $this->askConfirmOrder();
        });
    }

    private function askConfirmOrder()
    {
        $this->ask('Is this correct? Yes or No?', function ($answer) {
            $answer = $answer->getText();

            if ($answer == 'no') {
                $this->askOrder();
            } else {
                
                Lunch::create([
                    'name'  => $this->name,
                    'order' => $this->order
                ]);

                $this->say('Your order was saved! '.$this->order.' for '.$this->name.'.');
            }
        });
    }
}