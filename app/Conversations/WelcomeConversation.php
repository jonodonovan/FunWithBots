<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class WelcomeConversation extends Conversation
{
    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->ask('Hello, I\'m great, how are you?', function ($answer) {
            $this->answer = $answer->getText();

            if (trim($this->answer) === 'good' || trim($this->answer) === 'ok') {
                return $this->say('great');
            } elseif (trim($this->answer) === 'bad' || trim($this->answer) === 'not good') {
                return $this->say('Oh, that sucks. Get well soon.');
            }

            $this->say('Nice to meet you, '.ucfirst($this->name));
        });
    }
}
