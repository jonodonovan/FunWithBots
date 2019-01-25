<?php

namespace App\Conversations;

use App\Lunch;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class LunchConversation extends Conversation
{
    // Start the conversation
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

            $this->say('Nice to meet you, '.ucfirst($this->name));
            
            $menu = Question::create('Are you ready to submit your lunch order?')
                ->addButtons([
                    Button::create('Ready to order!')->value('ready'),
                    Button::create('Need more time!!')->value('needmoretime'),
                ]);

            $this->ask($menu, function ($answer) {
                $this->continue = $answer->getText();
    
                if ($this->continue == 'ready') {
                    $this->askOrder();
                } elseif ($this->continue == 'needmoretime') {
                    $this->say('Okay, the order will be submitted at 11am, please start the chat over again with "lunch" before then.');
                }
            });
        });
    }

    // Ask for order
    private function askOrder()
    {
        $this->ask('What would you like for lunch today?', function ($answer) {
            $this->order = $answer->getText();
            $this->say('Okay, I have a '.$this->order.' for '.ucfirst($this->name));
            $this->askNotes();
        });
    }

    // Ask for any special notes on the order
    private function askNotes()
    {

        $question = Question::create('Any special notes for the order? For example, no cheese or green beans as the side. Yes or no?')
            ->addButtons([
                Button::create('Yes')->value('yes'),
                Button::create('No')->value('no'),
            ]);

        $this->ask($question, function ($answer) {
            $this->notes = $answer->getText();

            if ($this->notes == 'yes') {
                $this->ask('What are the notes?', function ($answer) {
                    $this->note = $answer->getText();
                    $this->say('Okay, I have a '.$this->order.' with '.$this->note.' for '.ucfirst($this->name));
                    $this->askConfirmOrder();
                });
            } elseif ($this->notes == 'no') {
                $this->askConfirmOrderNoNotes();
            }
        });
    }

    // Confirmation of the order without notes
    private function askConfirmOrderNoNotes()
    {
        $this->say('Okay, I have a '.$this->order.' with no additional notes for '.ucfirst($this->name));
        $this->ask('Is this correct? Yes or No?', function ($answer) {
            $answer = $answer->getText();

            if ($answer == 'no') {
                $this->askOrder();
            } else {
                
                Lunch::create([
                    'name'  => $this->name,
                    'order' => $this->order
                ]);

                $this->say('Your order was saved! '.$this->order.' for '.ucfirst($this->name).'.');
            }
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
                    'order' => $this->order,
                    'notes' => $this->note
                ]);

                $this->say('Your order was saved! '.$this->order.' with '.$this->note.' for '.ucfirst($this->name).'.');
            }
        });
    }
}