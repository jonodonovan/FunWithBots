<?php

use App\Task;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Controllers\BotManController;
use BotMan\Drivers\AmazonAlexa\Extensions\Card;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

$botman = resolve('botman');

// ---------------------------------------
// 1. BASIC - Simple Chat ------------------
// ---------------------------------------
$botman->hears('hi|hello|hey', function ($bot) {
    $bot->reply('Hello!');
});


// ---------------------------------------
// 2. BASIC - Capturing User Input ---------
// ---------------------------------------
$botman->hears('My name is (.*)', function ($bot, $name) {
    $bot->reply('Hello '.ucfirst($name).'!');
});


// ---------------------------------------
// 3. BASIC - Having a Conversation and Database Integration ------------------
// ---------------------------------------
$botman->hears('Add a task', function ($bot) {
    $bot->ask('What is the name of the new task?', function ($answer, $conversation) {
        Task::create([
            'task'      => $answer,
            'user_id'   => $conversation->getBot()->getMessage()->getSender()
        ]);
        $conversation->say('A new task was created, called "'.$answer."'");
    });
});


// Show only my tasks
$botman->hears('Show my tasks', function ($bot) {
    $tasks = Task::where('completed', false)
        ->where('user_id', $bot->getMessage()->getSender())
        ->get();

    if (count($tasks) > 0) {
        $results = '';
        foreach ($tasks as $key =>$task) {
            $results .= $task->task. '<br>';
        }
        $bot->reply('Your tasks are: <br>'.$results);
    } else {
        $bot->reply('You do not have any tasks.');
    }
});


// Complete a task
$botman->hears('Task completed {id}', function ($bot, $id) {
    $task = Task::find($id);

    if (is_null($task)) {
        $bot->reply('Sorry, I could not find the task with ID of "'.$id.'"');
    } else {
        $task->completed = true;
        $task->save();

        $bot->reply('Task "'.$task->task.'" marked as completed');
    }
});


// Delete a task
$botman->hears('Delete task {id}', function($bot, $id) {
    $task = Task::find($id);

    if (is_null($task)) {
        $bot->reply('Sorry, I could not find the task with ID of "'.$id.'"');
    } else {
        $task->delete();

        $bot->reply('Task "'.$task->task.'" was deleted');
    }
});


// ---------------------------------------
// Other Conversation Examples ---------
// ---------------------------------------
$botman->hears('Lunch|OrderLunch', function ($bot) {
    $bot->startConversation(new App\Conversations\LunchConversation);
});

$botman->hears('Survey', function ($bot) {
    $bot->startConversation(new App\Conversations\SurveyConversation);
});


// ---------------------------------------
// 4. Using APIs - Get current weather ---------
// ---------------------------------------
$botman->hears('Weather in {location}(.*)', function ($bot, $location) {
    $url = 'http://api.weatherstack.com/current?access_key='.env('APIXU_TOKEN').'&query='.urlencode($location).'&units=f';
    $response = json_decode(file_get_contents($url));
    $bot->reply('The current weather in '.$response->location->name.' is '.$response->current->temperature.'°');
    // $bot->reply('With a condition of '.$response->current->weather_descriptions);
});

// Get forecast for multiple days
$botman->hears('{days} day forecast in {location}', function ($bot, $days, $location) {
    $url = 'https://api.apixu.com/v1/forecast.json?key='.env('APIXU_TOKEN').'&q='.urlencode($location).'&days='.urlencode($days);
    $response = json_decode(file_get_contents($url));
    $bot->reply('Temperature in '.$response->location->name.' for the next '.$days.' days:');

    foreach ($response->forecast->forecastday as $forecastday) {
        $bot->reply(date('D', $forecastday->date_epoch).' the '.date('jS', $forecastday->date_epoch).' is '.strval($forecastday->day->maxtemp_f));
    }
});


// ---------------------------------------
// 5. Messaging Platform Slide ---------
// ---------------------------------------


// ---------------------------------------
// 6. Using NLP Slide ---------
// ---------------------------------------
// $dialogflow = Dialogflow::create(env('DL_TOKEN'))->listenForAction();
// $botman->middleware->received($dialogflow);

// $botman->hears('weathersearch', function ($bot) {
//     $extras = $bot->getMessage()->getExtras();
//     $location = $extras['apiParameters']['geo-city'];

//     $url = 'https://api.apixu.com/v1/current.json?key='.env('APIXU_TOKEN').'&q='.urlencode($location);
//     $response = json_decode(file_get_contents($url));
//     $bot->reply('The current weather in '.$response->location->name.' is '.$response->current->temp_f.'°');
//     $bot->reply('With a condition of '.$response->current->condition->text);
// })->middleware($dialogflow);


// ---------------------------------------
// 7. Using Alexa Slide ---------
// ---------------------------------------
$botman->hears('showtasks', function ($bot) {
    $tasks = Task::where('completed', false)
        ->where('user_id', $bot->getMessage()->getSender())
        ->get();

    if (count($tasks) > 0) {

        $results = '';
        foreach ($tasks as $key =>$task) {
            $results .= $task->id.' - '.$task->task.",\n";
        }
        $card = Card::create('Your tasks are:')
            ->type(Card::STANDARD_CARD_TYPE)
            ->image('https://funwithbots.jodonovan.com/images/robot-light-blue.png')
            ->text("\n".$results);

        $message = OutgoingMessage::create('Your tasks are: '."\n".$results)->withAttachment($card);
        $bot->reply($message);

    } else {
        $bot->reply('All your tasks have been completed.');
    }
});


$botman->hears('addtask', function ($bot) {

    $slots = $bot->getMessage()->getExtras('slots');
    $task = $slots['Task']['value'];

    Task::create([
        'task'      => $task,
        'user_id'   => $bot->getMessage()->getSender()
    ]);

    $bot->reply('You added a new task called "'. $task ."'");
});










// ---------------------------------------
// Support Commands ---------------
// ---------------------------------------


// Other converdsations
$botman->hears('how are you(.*)', function ($bot) {
    $bot->startConversation(new App\Conversations\WelcomeConversation);
});

// Help Menu
$botman->hears('your name|what\'s your name(.*)|what is your name(.*)', function ($bot) {
    $bot->reply('Hello, I am the "Fun with Bots" bot.');
});

// Stopping a conversation
$botman->hears('stop', function ($bot) {
    $bot->reply('Conversation stopped');
})->stopsConversation();

// Help Menu
$botman->hears('start|menu', function ($bot) {
    $bot->reply('Say something like "Hi" or "My name is..."');
});

// Facebook Tester Specific Commands
$botman->hears('what can you do', function ($bot) {
    $bot->reply('I do many things Alejandro, read my Facebook App submission notes for examples.');
});
$botman->hears('help', function ($bot) {
    $bot->reply('What do you need help with? Say something like "Hi" or "My name is..."');
});

// Fallback
$botman->fallback(function($bot) {
    $bot->reply('Command not found. Say something like "Hi" or "My name is..."');
});

// ---------------------------------------
// Not Used ---------------
// ---------------------------------------

// $botman->hears('Tell me something good', BotManController::class.'@startConversation');

// // Add a task with a conversation with user ID
// $botman->hears('New task', function ($bot) {
//     $bot->ask('What is the name of the new task?', function ($answer, $conversation) {
//         Task::create([
//             'task'      => $answer,
//             'user_id'   => $conversation->getBot()->getMessage()->getSender()
//         ]);
//         $conversation->say('A new task was created, called "'.$answer."'");
//     });
// });


// Add a task inline
// $botman->hears('Add a new task called {task}', function ($bot, $task) {
//     Task::create([
//         'task' => $task
//     ]);
//     $bot->reply('You added a new task called "'.$task."'");
// });


// Show all tasks that are completed
// $botman->hears('Show tasks', function ($bot) {
//     $tasks = Task::where('completed', false)->get();

//     if (count($tasks) > 0) {
//         $bot->reply('Your tasks are:');
//         foreach ($tasks as $task) {
//             $bot->reply($task->id.' - '.$task->task);
//         }
//     } else {
//         $bot->reply('You do not have any tasks.');
//     }
// });
