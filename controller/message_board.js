var pg = require ('pg');

var pgConString = "postgres://workfar:password@localhost/workfar";
var WebSocketServer = require('ws').Server, wss = new WebSocketServer({ port: 1337 });

pg.connect(pgConString, function(err, client) {
	wss.on('connection', function connection(ws) {
		client.on('notification', function(msg) {
			var message = msg['payload'].toString().split(",")[0];
			var user_id = msg['payload'].toString().split(",")[1];
			console.log(ws);
			ws.send(message);
		});
		ws.on('close', function close() {
		  ws.terminate();
		  console.log('disconnected');
		});
		ws.on('message', function incoming(message) {
			console.log('received: %s', message);
		});

	});
	var query = client.query("LISTEN new_task");
});