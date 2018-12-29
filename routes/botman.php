<?php
use App\Todo;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');


// // Simple example
// $botman->hears('Hi', function ($bot) {
//     $bot->reply('Hello!');
// });


// // Using variables
// $botman->hears('My name is {name}', function ($bot, $name) {
//     $bot->reply('Hello, '.$name.'!');
// });


// // Get current weather
// $botman->hears('Weather in {location}', function ($bot, $location) {
//     $url = 'https://api.apixu.com/v1/current.json?key=4d64a49597bb4db18b4233225182812&q='.urlencode($location);
//     $response = json_decode(file_get_contents($url));
//     $bot->reply('The current weather in '.$response->location->name.' is '.$response->current->temp_f.'°');
//     $bot->reply('With a condition of '.$response->current->condition->text);
// });


// // Get forecast for multiple days
// $botman->hears('{days} day forecast in {location}', function ($bot, $days, $location) {
    
//     $url = 'https://api.apixu.com/v1/forecast.json?key=4d64a49597bb4db18b4233225182812&q='.urlencode($location).'&days='.urlencode($days);
//     $response = json_decode(file_get_contents($url));
//     $bot->reply('Temperature in '.$response->location->name.' for the next '.$days.' days:');
    
//     foreach ($response->forecast->forecastday as $forecastday) {
//         $bot->reply(date('D', $forecastday->date_epoch).' the '.date('jS', $forecastday->date_epoch).' is '.strval($forecastday->day->maxtemp_f));
//     }
// });


// // Start a new conversation
// $botman->hears('Let\'s have some fun', BotManController::class.'@startConversation');


// // Show all todos that are completed
// $botman->hears('Show todos', function ($bot) {
//     $todos = Todo::where('completed', false)->get();
    
//     if (count($todos) > 0) {
//         $bot->reply('Your todos are:');
//         foreach ($todos as $todo) {
//             $bot->reply($todo->id.' - '.$todo->task);
//         }
//     } else {
//         $bot->reply('You do not have any todos.');
//     }
// });


// // Add a todo inline
// $botman->hears('Add a new todo {task}', function ($bot, $task) {
//     Todo::create([
//         'task' => $task
//     ]);
//     $bot->reply('You added a new todo called "'.$task."'");
// });


// // Add a todo with a conversation
// $botman->hears('Add a todo', function ($bot) {
//     $bot->ask('What is the name of the new todo?', function($answer, $conversation) {
//         Todo::create([
//             'task' => $answer
//         ]);
//         $conversation->say('A new todo was created, called "'.$answer."'");
//     });
// });


// // Complete a todo 
// $botman->hears('Todo completed {id}', function($bot, $id) {
//     $todo = Todo::find($id);

//     if (is_null($todo)) {
//         $bot->reply('Sorry, I could not find the todo with ID of "'.$id.'"');
//     } else {
//         $todo->completed = true;
//         $todo->save();

//         $bot->reply('Todo "'.$todo->task.'" marked as completed');
//     }
// });


// // Delete a todo 
// $botman->hears('Delete todo {id}', function($bot, $id) {
//     $todo = Todo::find($id);

//     if (is_null($todo)) {
//         $bot->reply('Sorry, I could not find the todo with ID of "'.$id.'"');
//     } else {
//         $todo->delete();

//         $bot->reply('Todo "'.$todo->task.'" was deleted');
//     }
// });


// // Add a todo with a conversation with user ID
// $botman->hears('New todo', function ($bot) {
//     $bot->ask('What is the name of the new todo?', function($answer, $conversation) {
//         Todo::create([
//             'task'      => $answer,
//             'user_id'   => $conversation->getBot()->getMessage()->getSender()
//         ]);
//         $conversation->say('A new todo was created, called "'.$answer."'");
//     });
// });


// // Show only my todos
// $botman->hears('Show my todos', function ($bot) {
//     $todos = Todo::where('completed', false)
//         ->where('user_id', $bot->getMessage()->getSender())
//         ->get();
    
//     if (count($todos) > 0) {
//         $bot->reply('Your todos are:');
//         foreach ($todos as $todo) {
//             $bot->reply($todo->id.' - '.$todo->task);
//         }
//     } else {
//         $bot->reply('You do not have any todos.');
//     }
// });


// Getting weather using AI
$dialogflow = Dialogflow::create('0d45c829ec3a427491778dadfe0fdcd1')->listenForAction();
$botman->middleware->received($dialogflow);

$botman->hears('weathersearch', function ($bot) {
    $extras = $bot->getMessage()->getExtras();
    $location = $extras['apiParameters']['geo-city'];

    $url = 'https://api.apixu.com/v1/current.json?key=4d64a49597bb4db18b4233225182812&q='.urlencode($location);
    $response = json_decode(file_get_contents($url));
    $bot->reply('The current weather in '.$response->location->name.' is '.$response->current->temp_f.'°');
    $bot->reply('With a condition of '.$response->current->condition->text);
})->middleware($dialogflow);