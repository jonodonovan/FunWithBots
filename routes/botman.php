<?php

use App\Task;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

// ---------------------------------------
// Simple Chat ------------------
// ---------------------------------------
$botman->hears('hi|hello|hey', function ($bot) {
    $bot->reply('Hello!');
});


// ---------------------------------------
// Campturing User Response ---------
// ---------------------------------------
$botman->hears('My name is (.*)', function ($bot, $name) {
    $bot->reply('Hello '.ucfirst($name).'!');
});


// Add a task with a conversation
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
// Conversations ---------
// ---------------------------------------
$botman->hears('Lunch|OrderLunch', function ($bot) {
    $bot->startConversation(new App\Conversations\LunchConversation);
});


// ---------------------------------------
// Using APIs - Get current weather ---------
// ---------------------------------------
$botman->hears('Weather in {location}', function ($bot, $location) {
    $url = 'https://api.apixu.com/v1/current.json?key='.env('APIXU_TOKEN').'&q='.urlencode($location);
    $response = json_decode(file_get_contents($url));
    $bot->reply('The current weather in '.$response->location->name.' is '.$response->current->temp_f.'°');
    $bot->reply('With a condition of '.$response->current->condition->text);
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
// Using NPL ---------
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
// Using Alexa ---------
// ---------------------------------------
$botman->hears('showtasks', function ($bot) {
    $tasks = Task::where('completed', false)
        ->where('user_id', $bot->getMessage()->getSender())
        ->get();
    
    if (count($tasks) > 0) {
        
        $results = '';
        foreach ($tasks as $key =>$task) {
            $results .= $task->task.'\r\n';
        }
        $bot->reply('Your tasks are:\r\n'.$results);

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

// // Start a new conversation 2
// $botman->hears('Survey', function ($bot) {
//     $bot->startConversation(new App\Conversations\Survey);
// });

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