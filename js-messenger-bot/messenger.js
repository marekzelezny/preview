'use strict';

const
	request = require('request'),
	labels = require('./labels'),
	config = require('./config');

const
	bot = {
		message: require('./messenger/message'),
		broadcast: require('./messenger/broadcast'),
		handover: require('./messenger/handover'),
		user: require('./messenger/user')
	};

// Nastavení bota pro Messenger
function botSetup() {
	let data = {
		"get_started": {
			"payload": "get_started"
		},
		"greeting":[
		  {
			"locale":"default",
    		"text": labels.greetings
		  }
		],
		"persistent_menu":[
		  {
			"locale": "default",
			"composer_input_disabled": true,
			"call_to_actions":[
	          {
				"title": labels.persistent_menu.notifications_title,
				"type":"nested",
				"call_to_actions":[
	            {
					"title": labels.persistent_menu.notifications_messenger,
					"type":"postback",
					"payload": config.payload.notifications.messenger
	            },
	            {
					"title": labels.persistent_menu.notifications_pc,
					"type":"web_url",
					"url": config.urls.notifikace
	            },
	            {
					"title": labels.persistent_menu.notifications_email,
					"type":"web_url",
					"url": config.urls.newsletterSignUp
	            },
	          ]
	         },
	         {
				"title": labels.persistent_menu.donation_title,
				"type":"nested",
				"call_to_actions":[
	            {
		         	"title": labels.persistent_menu.donation_sbirka,
					"type":"web_url",
					"url": config.urls.sbirka
		         },
		         {
		         	"title": labels.persistent_menu.donation_revue,
					"type":"web_url",
					"url": config.urls.revue
		         }
	          ]
	         }
	        ]
		  }
		]
	}

	request({
		"uri": config.fb.uri.host + "/694807680694998/messenger_profile",
		"qs": { "access_token": config.fb.accessToken },
		"method": "POST",
		"json": data
	}, (err, res, body) => {
		if (!err) {
			console.log('Data:' + JSON.stringify(data, null, 4));
			console.log('Body:' + JSON.stringify(body, null, 4));
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
}

// Zobrazení nastavení bota pro Messenger
function sendBotSetup() {
	request({
		"uri": config.fb.uri.host + "/me/messenger_profile",
		"qs": { 
			"access_token": config.fb.accessToken,
			"fields": "persistent_menu"
		},
		"method": "GET"
	}, (err, res, body) => {
		if (!err) {
			console.log('Body:' + JSON.stringify(body, null, 4));
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
}

// Odešle prvotní zprávu po kliknutí na "Get started" (Začněte)
function sendGetStarted(sender_psid) {

	/*let options = {
		attachment: {
			type: "template",
			payload: {
				template_type: "button",
				text: "Nyní si prosím vyberte jednu z nabízených možností",
				buttons:[
				{
					type: "postback",
					title: "Odběr notifikací",
					payload: config.payload.notifications.messenger
				}, {
					type: "postback",
					title: "Kontaktovat redakci",
					payload: config.payload.handover.info
				}]
			}
		}
	}*/

	bot.message.sendMedia(sender_psid, labels.media.alligator, 'image', function() {
		bot.message.sendMessage(sender_psid, labels.messages.hello, 1000);
		bot.message.sendMessage(sender_psid, labels.messages.getStarted, 1500);
		setTimeout(function() {
		    bot.broadcast.sendInfo(sender_psid);
		}, 1500);
	});

}

function sendReturnBackMessage(sender_psid) {
	setTimeout(function() {
	    bot.message.sendMessage(sender_psid, 'I to se stává, ale nevadí.. 🙂 Kdybyste si to někdy rozmysleli, tak se můžete přihlásit kliknutím na "Nastavení odběru novinek" v menu dole 👇');
	}, 1000);
}

// Spravuje všechny příchozí zprávy typu zpráva
function handleMessage(sender_psid, message) {

	//message = message.text.toLowerCase();

	console.log("handleMessage:", message);

	switch( message ) {

		case 'test':
			bot.message.sendMessage(sender_psid, "");
			break;

		default:
			bot.message.sendMessage(sender_psid, "");

	}

}

// Spravuje všechny příchozí zprávy jiného typu než zpráva
function handlePostback(sender_psid, received_item) {

	let payload = received_item.payload;
	let message = received_item.title;

	switch( payload ) {

		case config.payload.getStarted:
			sendGetStarted(sender_psid);
			break;

		case config.payload.notifications.messenger:
			//bot.broadcast.sendInfo(sender_psid);
			let attachment = {
				attachment: {
					type: "template",
					payload: {
						template_type: "generic",
						elements:[
						{
							title: labels.broadcast.options.title,
							buttons:[
							{
								type: "postback",
								title: labels.broadcast.options.yes,
								payload: config.payload.notifications.signIn
							}, {
								type: "postback",
								title: labels.broadcast.options.signOut,
								payload: config.payload.notifications.signOut
							}]
						}],
					}
				},
			}
			bot.message.callSendAPI(sender_psid, attachment);
			break;

		case config.payload.returnBack:
			sendReturnBackMessage(sender_psid);
			break;

		case config.payload.notifications.signIn:
			bot.broadcast.labelSignIn(sender_psid, config.labels.notifikace);
			break;

		case config.payload.notifications.signOut:
			bot.broadcast.labelSignOut(sender_psid, config.labels.notifikace);
			break;

		case config.payload.handover.info:
			bot.handover.sendInfo(sender_psid);
			break;

		case config.payload.handover.passToInbox:
			bot.message.senderAction(sender_psid, 'typing_on');
			bot.handover.passToInbox(sender_psid);
			break;

	}

}

module.exports = {
	botSetup,
	sendBotSetup,
	handleMessage,
	handlePostback
};
