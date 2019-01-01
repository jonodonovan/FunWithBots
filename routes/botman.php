<?php
use App\Todo;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');


// Simple example
$botman->hears('Hi', function ($bot) {
    $bot->reply('Hello!');
});


// Using variables
$botman->hears('My name is (.*)', function ($bot, $name) {
    $bot->reply('Hello '.$name.'!');
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


// Show all todos that are completed
$botman->hears('Show to-dos', function ($bot) {
    $todos = Todo::where('completed', false)->get();
    
    if (count($todos) > 0) {
        $bot->reply('Your to-dos are:');
        foreach ($todos as $todo) {
            $bot->reply($todo->id.' - '.$todo->task);
        }
    } else {
        $bot->reply('You do not have any to-dos.');
    }
});


// Add a todo inline
$botman->hears('Add a new to-do {task}', function ($bot, $task) {
    Todo::create([
        'task' => $task
    ]);
    $bot->reply('You added a new to-do called "'.$task."'");
});


// Add a todo with a conversation
$botman->hears('Add a to-do', function ($bot) {
    $bot->ask('What is the name of the new to-do?', function ($answer, $conversation) {
        Todo::create([
            'task' => $answer,
            'user_id'   => $conversation->getBot()->getMessage()->getSender()
        ]);
        $conversation->say('A new to-do was created, called "'.$answer."'");
    });
});


// Complete a todo 
$botman->hears('To-do completed {id}', function ($bot, $id) {
    $todo = Todo::find($id);

    if (is_null($todo)) {
        $bot->reply('Sorry, I could not find the to-do with ID of "'.$id.'"');
    } else {
        $todo->completed = true;
        $todo->save();

        $bot->reply('To-do "'.$todo->task.'" marked as completed');
    }
});


// Delete a todo 
$botman->hears('Delete to-do {id}', function($bot, $id) {
    $todo = Todo::find($id);

    if (is_null($todo)) {
        $bot->reply('Sorry, I could not find the to-do with ID of "'.$id.'"');
    } else {
        $todo->delete();

        $bot->reply('To-do "'.$todo->task.'" was deleted');
    }
});


// Add a todo with a conversation with user ID
$botman->hears('New to-do', function ($bot) {
    $bot->ask('What is the name of the new to-do?', function ($answer, $conversation) {
        Todo::create([
            'task'      => $answer,
            'user_id'   => $conversation->getBot()->getMessage()->getSender()
        ]);
        $conversation->say('A new to-do was created, called "'.$answer."'");
    });
});


// Show only my todos
$botman->hears('Show my to-dos', function ($bot) {
    $todos = Todo::where('completed', false)
        ->where('user_id', $bot->getMessage()->getSender())
        ->get();
    
    if (count($todos) > 0) {
        $bot->reply('Your to-dos are:');
        foreach ($todos as $todo) {
            $bot->reply($todo->id.' - '.$todo->task);
        }
    } else {
        $bot->reply('You do not have any to-dos.');
    }
});


// Adding a Help option
$botman->hears('help', function ($bot) {
    $bot->reply('How can I help you?');
})->skipsConversation();


// Stopping a conversation
$botman->hears('stop', function ($bot) {
    $bot->reply('Conversation stopped');
})->stopsConversation();


// Getting weather using AI
// $dialogflow = Dialogflow::create('0d45c829ec3a427491778dadfe0fdcd1')->listenForAction();
// $botman->middleware->received($dialogflow);

// $botman->hears('weathersearch', function ($bot) {
//     $extras = $bot->getMessage()->getExtras();
//     $location = $extras['apiParameters']['geo-city'];

//     $url = 'https://api.apixu.com/v1/current.json?key=4d64a49597bb4db18b4233225182812&q='.urlencode($location);
//     $response = json_decode(file_get_contents($url));
//     $bot->reply('The current weather in '.$response->location->name.' is '.$response->current->temp_f.'°');
//     $bot->reply('With a condition of '.$response->current->condition->text);
// })->middleware($dialogflow);


// Using Alexa
$botman->hears('MyTodos', function ($bot) {
    $todos = Todo::where('completed', false)
        ->where('user_id', $bot->getMessage()->getSender())
        ->get();
    
    if (count($todos) > 0) {
        $bot->reply('Your to-dos are:');
        foreach ($todos as $todo) {
            $bot->reply($todo->id.' - '.$todo->task);
        }
    } else {
        $bot->reply('All your to-dos have been completed.');
    }
});