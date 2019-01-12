<?php
use App\task;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');


// Simple example
$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});


// Using variables
$botman->hears('My name is (.*)', function ($bot, $name) {
    $bot->reply('Hello '.ucfirst($name).'!');
});


// Get current weather
$botman->hears('Weather in {location}', function ($bot, $location) {
    $url = 'https://api.apixu.com/v1/current.json?key=4d64a49597bb4db18b4233225182812&q='.urlencode($location);
    $response = json_decode(file_get_contents($url));
    $bot->reply('The current weather in '.$response->location->name.' is '.$response->current->temp_f.'°');
    $bot->reply('With a condition of '.$response->current->condition->text);
});


// Get forecast for multiple days
$botman->hears('{days} day forecast in {location}', function ($bot, $days, $location) {
    
    $url = 'https://api.apixu.com/v1/forecast.json?key=4d64a49597bb4db18b4233225182812&q='.urlencode($location).'&days='.urlencode($days);
    $response = json_decode(file_get_contents($url));
    $bot->reply('Temperature in '.$response->location->name.' for the next '.$days.' days:');
    
    foreach ($response->forecast->forecastday as $forecastday) {
        $bot->reply(date('D', $forecastday->date_epoch).' the '.date('jS', $forecastday->date_epoch).' is '.strval($forecastday->day->maxtemp_f));
    }
});


// Start a new conversation
$botman->hears('Party time', BotManController::class.'@startConversation');

// Start a new conversation 2
$botman->hears('Survey', function ($bot) {
    $bot->startConversation(new App\Conversations\Survey);
});


// Show all tasks that are completed
$botman->hears('Show tasks', function ($bot) {
    $tasks = Task::where('completed', false)->get();
    
    if (count($tasks) > 0) {
        $bot->reply('Your tasks are:');
        foreach ($tasks as $task) {
            $bot->reply($task->id.' - '.$task->task);
        }
    } else {
        $bot->reply('You do not have any tasks.');
    }
});


// Add a task inline
$botman->hears('Add a new task called {task}', function ($bot, $task) {
    Task::create([
        'task' => $task
    ]);
    $bot->reply('You added a new task called "'.$task."'");
});


// Add a task with a conversation
$botman->hears('Add a task', function ($bot) {
    $bot->ask('What is the name of the new task?', function ($answer, $conversation) {
        Task::create([
            'task' => $answer,
            'user_id'   => $conversation->getBot()->getMessage()->getSender()
        ]);
        $conversation->say('A new task was created, called "'.$answer."'");
    });
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


// Add a task with a conversation with user ID
$botman->hears('New task', function ($bot) {
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
        $bot->reply('Your tasks are:');
        foreach ($tasks as $task) {
            $bot->reply($task->id.' - '.$task->task);
        }
    } else {
        $bot->reply('You do not have any tasks.');
    }
});


// Stopping a conversation
$botman->hears('stop', function ($bot) {
    $bot->reply('Conversation stopped');
})->stopsConversation();


// Using Alexa
$botman->hears('MyTasks', function ($bot) {
    $tasks = Task::where('completed', false)
        ->where('user_id', $bot->getMessage()->getSender())
        ->get();
    
    if (count($tasks) > 0) {
        $bot->reply('Your tasks are:');
        foreach ($tasks as $task) {
            $bot->reply($task->id.' - '.$task->task);
        }
    } else {
        $bot->reply('All your tasks have been completed.');
    }
});


// Ordering Lunch
$botman->hears('Lunch|OrderLunch', function ($bot) {
    $bot->startConversation(new App\Conversations\LunchConversation);
});


// Help Menu
$botman->hears('help', function ($bot) {
    $bot->reply('Say something like "Hi" or "My name is..."');
});


// Fallback
$botman->fallback(function($bot) {
    $bot->reply('Try another command or type "help". That command has not been added.');
});