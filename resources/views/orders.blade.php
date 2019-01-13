<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no" />
		<link rel="icon"
		type="image/png"
		href="images/robot-white.png">

		<title>Fun With Bots</title>
		<link rel="stylesheet" href="css/app.css">
		
	</head>
	<body>
		<div class="container-fluid">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Order</th>
                        <th scope="col">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @if (! $orders->isEmpty())
                        @foreach ($orders as $order)
                        <tr>
                            <th scope="row">@php echo $i; @endphp</th>
                            <td>{{$order->name}}</td>
                            <td>{{$order->order}}</td>
                            <td>{{$order->notes}}</td> 
                        </tr>
                        @php $i++; @endphp
                        @endforeach
                    @else
                        No orders found... try again later
                    @endif
                </tbody>
            </table>
            
        </div>
		<script src="js/app.js"></script>
	</body>
</html>