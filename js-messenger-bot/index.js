'use strict';

const config = require('./config');

const
	bot = {
		message: require('./messenger/message'),
		broadcast: require('./messenger/broadcast'),
		handover: require('./messenger/handover'),
		user: require('./messenger/user')
	};

const
	messenger = require('./messenger'),
	https = require('https'),
	express = require('express'),
	bodyParser = require('body-parser'),
	app = express().use(bodyParser.json()); // creates express http server

var port = process.env.PORT || 8080;

app.listen(port, function () {
  console.log('Webhook is listening on port ' + port);
});

// Shows content on browser open
app.get('/', (req, res) => {
	res.send("Hello World");
});

// POST: Messenger
app.post('/messenger', (req, res) => {  
 
  let body = req.body;

  console.log(JSON.stringify(body, null, 4));

  // Checks this is an event from a page subscription
  if (body.object === 'page') {

    // Iterates over each entry - there may be multiple if batched
    body.entry.forEach(function(entry) {

		// Gets the message. entry.messaging is an array, but 
		// will only ever contain one message, so we get index 0
		console.log(entry);

		if( entry.messaging ) {
			let webhook_event = entry.messaging[0];
			let sender_psid = webhook_event.sender.id;

			console.log("entry.messaging:", webhook_event);

			if (webhook_event.message) {

				if(webhook_event.message.quick_reply) {
					messenger.handlePostback(sender_psid, webhook_event.message.quick_reply);
				} else {
					messenger.handleMessage(sender_psid, webhook_event.message);
				}

			} else if ( webhook_event.postback ) {
				messenger.handlePostback(sender_psid, webhook_event.postback);
			} else if ( webhook_event.pass_thread_control.metadata === 'Pass thread control from Page Inbox' ) {
				bot.message.sendMessage(sender_psid, "Komunikace s redakcí byla ukončena. Prosím využijte nabídku umístěnou úplně dole.");
			}

		} else if ( entry.standby ) {
			console.log("standby:", entry.standby[0]);
			let webhook_event = entry.standby[0];
		}
    });

    // Returns a '200 OK' response to all requests
    res.status(200).send('EVENT_RECEIVED');
  } else {
    // Returns a '404 Not Found' if event is not from a page subscription
    res.sendStatus(404);
  }

});

// GET: Messenger
app.get('/messenger', (req, res) => {
    
  // Parse the query params
  let mode = req.query['hub.mode'];
  let token = req.query['hub.verify_token'];
  let challenge = req.query['hub.challenge'];
    
  // Checks if a token and mode is in the query string of the request
  if (mode && token) {
  
    // Checks the mode and token sent is correct
    if (mode === 'subscribe' && token === config.fb.verifyToken) {
      
      // Responds with the challenge token from the request
      console.log('WEBHOOK_VERIFIED');
      res.status(200).send(challenge);
    
    } else {
      // Responds with '403 Forbidden' if verify tokens do not match
      res.sendStatus(403);      
    }
  }
});

app.post('/broadcastMessage', (req, res) => {
	let auth = req.headers['authorization'];
	let body = req.body;
	var success = { "success": true }

	//messenger.broadcastMessage(query.label);

	//console.log(JSON.stringify(auth, null, 4));
	//console.log('query: ' + body.title);

	if( auth && body ) {

		if( auth === config.fb.broadcastToken ) {
			bot.broadcast.sendArticle(config.labels.notifikace, body.title, body.image_url, body.subtitle, body.url);
			res.status(200).send(success);
		} else {
			res.sendStatus(403);  
		}

	} else {
		res.sendStatus(403);  
	}
	//console.log('query:' + JSON.stringify(req.fields, null, 4));
});

app.get('/preposlat', (req, res) => {
	let query = req.query;
	console.log(query);
	bot.message.sendURL('1558727770908321', query.url);
	res.send("Hello World");
});

app.get('/uzivatel', (req, res) => {
	let query = req.query;
	console.log(query);
	res.send("Hello World");
	
	bot.user.getUserInfo(query.psid, function(data) {
		res.send(data);
	});
});

app.get('/botsetup', (req, res) => {
	messenger.botSetup();
	res.send("Hotovo");
});

app.get('/botpreview', (req, res) => {
	messenger.sendBotSetup();
	res.send("Hotovo");
});

app.get('/getLabels', (req, res) => {
	bot.broadcast.getLabels();
	res.send("Hotovo");
});

app.get('/labelCreate', (req, res) => {
	let query = req.query;
	bot.broadcast.labelCreate(query.label);
	res.send("Hotovo:");
});

app.get('/labelSignIn', (req, res) => {
	let query = req.query;
	bot.broadcast.labelSignIn(query.psid, query.label, function(data) {
		res.send(data);
	});
});

app.get('/labelSignOut', (req, res) => {
	let query = req.query;
	bot.broadcast.labelSignOut(query.psid, query.label, function(data) {
		res.send(data);
	});
});
