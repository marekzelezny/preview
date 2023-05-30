'use strict';

const
	request = require('request'),
	config = require('../config');

const
	bot = {
		message: require('./message'),
		broadcast: require('./broadcast'),
		handover: require('./handover'),
		user: require('./user')
	};

// Odešle informace o možnosti přepnutí komunikace
function sendInfo(sender_psid) {
	
	bot.message.sendMessage(
		sender_psid, 
		'Komunikace dosud byla vedena pouze s počítačem bez asistance redakce. Pokud si přejete napsat přímo redakci, tak prosím klikněte na tlačítko Ano chci a vyčkejte na potvrzení.', 
		1000
	);

	let options = {
		attachment: {
			type: "template",
			payload: {
				template_type: "button",
				text: "🗣 Chcete napsat zprávu redakci?",
				buttons:[
				{
					type: "postback",
					title: "Ano, chci",
					payload: config.payload.handover.passToInbox
				}, {
					type: "postback",
					title: "Ne, nechci",
					payload: config.payload.returnBack
				}]
			}
		}
	}

	setTimeout(function() {
		bot.message.callSendAPI(sender_psid, options);
	}, 1500);
}

// POST: Přepošle komunikaci do Page Inbox
function passToInbox(sender_psid) {
	let data = {
		"recipient": {
	  		"id": sender_psid
		},
		"target_app_id": config.labels.pageInboxThread,
		"metadata": "String to pass to secondary receiver app"
	}

	request({
		"uri": config.fb.uri.host + "/me/pass_thread_control",
		"qs": { "access_token": config.fb.accessToken },
		"method": "POST",
		"json": data
	}, (err, res, body) => {
		if (!err) {
	  		console.log('Následující zpráva byla odeslána:', data);
	  		console.log(body);

	  		setTimeout(function() {
			    bot.message.sendMessage(sender_psid, 'Byli jste úspěšně propojeni s redakcí. Nyní prosím napište vaši zprávu a vyčkejte na odpověď.');
			}, 1000);

		} else {
	  		console.error("Došlo k chybě:", err);
	  		console.error(body);
		}
	});
}

module.exports = {
	sendInfo,
	passToInbox
};
