<html>
	<head>
		 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	</head>
	<body>
		<div id="output"></div>
		<script type="text/javascript">
		$(function() {
			$.ajaxSetup({
				headers: {'AUTHTOKEN' : 'bb9b7afc4b246f19f202b96d5e70f59ba916cfbf'}
			});

			$.ajax({

				//url: 'login',
				//url: 'clients/002538/matters',
				//url: 'users/1/works',
				//url: 'users/1/works/23643',
				//url: 'users/1/device',
				url: 'users/1/device/bb9b7afc 4b246f19 f202b96d 5e70f59b a916cfbf',

				/*data: {
					'user' : '99511620',
					'password' : 'admin.asdwsx',
					'app_key' : 'https://lemontech.thetimebilling.com'
				},*/
				//data: {'after' : '1364871600', 'before' : '1364871600'},
				/*data: {
					'created_date' : 1365193560.328282,
					'date' : 1365134400,
					'duration' : 100,
					'notes' : 'creaci�n en latin1',
					//'rate' : 1,
					//'requester' : 'test1 test2 test3',
					//'activity_code' : 'A2013',
					//'area_code' : 1,
					'matter_code' : '0001-0001',
					//'task_code' : 1,
					'user_id' : 1,
					'billable' : 1,
					'visible' : 0
				},*/
				//data: { 'token' : 'bb9b7afc 4b246f19 f202b96d 5e70f59b a916cfbf' },

				//type: 'get'
				//type: 'post'
				//type: 'put'
				type: 'delete'

			}).done(function(data) {
				//console.log(data);
				//$('#output').html(data);
				$('#output').html(JSON.stringify(data));
			});
		});
		</script>
	</body>
</html>
