'use strict';

const
	request = require('request'),
	labels = require('../labels'),
	config = require('../config');

const
	bot = {
		message: require('./message'),
		broadcast: require('./broadcast'),
		handover: require('./handover'),
		user: require('./user')
	};

// Odešle informace o možnostech odběru novinek
function sendInfo(sender_psid) {

	/*setTimeout(function() {
	    bot.message.sendMessage(sender_psid, labels.broadcast.info);
	}, 1000);*/

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
						title: labels.broadcast.options.no,
						payload: config.payload.returnBack
					}, {
						type: "postback",
						title: labels.broadcast.options.signOut,
						payload: config.payload.notifications.signOut
					}]
				}],
			}
		},
	}

	let quickReplies = [
		{
			"content_type": "text",
			"title": labels.broadcast.options.yes,
			"payload": config.payload.notifications.signIn,
		},
		{
			"content_type": "text",
			"title": labels.broadcast.options.no,
			"payload": config.payload.returnBack,
		},
		{
			"content_type": "text",
			"title": labels.broadcast.options.signOut,
			"payload": config.payload.notifications.signOut,
		},
	]

	setTimeout(function() {
		//bot.message.callSendAPI(sender_psid, attachment);
		//bot.message.sendMessage(sender_psid, labels.broadcast.info);
		bot.message.sendQuickReplies(sender_psid, labels.broadcast.info, quickReplies);
	}, 1000);

}

/**************************/
/*         LABELS         */
/**************************/

// POST: Vytvoří label
function labelCreate(label) {
	request({
		"uri": config.fb.uri.hostBroadcast + "/me/custom_labels",
		"qs": { 
			"name": label,
			"access_token": config.fb.accessToken 
		},
		"method": "POST"
	}, (err, res, body) => {
		if (!err && res.statusCode == 200) {
	  		console.log('broadcastLabelCreate:' + JSON.stringify(body));
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
}

// GET: Získá labels
function getLabels() {
	request({
		"uri": config.fb.uri.hostBroadcast + "/me/custom_labels",
		"qs": { 
			"fields": "name",
			"access_token": config.fb.accessToken 
		},
		"method": "GET"
	}, (err, res, body) => {
		if (!err && res.statusCode == 200) {
	  		console.log('getBroadcastLabels:' + JSON.stringify(body, null, 4));
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
}

// POST: Přidá uživatele do labelu
function labelSignIn(sender_psid, label) {
	request({
		"uri": config.fb.uri.host + label + "/label",
		"qs": {
			"access_token": config.fb.accessToken 
		},
		"json": {
			"user": sender_psid
		},
		"method": "POST"
	}, (err, res, body) => {
		if (!err && res.statusCode == 200) {
			console.log('labelSignIn:' + JSON.stringify(body, null, 4));
			setTimeout(function() {
				bot.message.sendMedia(sender_psid, labels.media.clap, 'image', function() {
					bot.message.sendMessage(sender_psid, labels.broadcast.response.subscribed, 1000);
					bot.message.sendMessage(sender_psid, labels.broadcast.response.afterInfo, 1500);
				});
			}, 1000);
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
}

// DELETE: Odstraní uživatele z labelu
function labelSignOut(sender_psid, label) {
	request({
		"uri": config.fb.uri.host + label + "/label",
		"qs": {
			"access_token": config.fb.accessToken 
		},
		"json": {
			"user": sender_psid
		},
		"method": "DELETE"
	}, (err, res, body) => {
		if (!err && res.statusCode == 200) {
			console.log('labelSignOut:' + JSON.stringify(body, null, 4));
			setTimeout(function() {
				bot.message.sendMedia(sender_psid, labels.media.bye, 'image', function() {
					bot.message.sendMessage(sender_psid, labels.broadcast.response.unsubscribed, 1000);
					bot.message.sendMessage(sender_psid, labels.broadcast.response.afterInfo, 1500);
				});
			}, 1000);
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
}

/**************************/
/*       MESSAGES         */
/**************************/

// POST: Odešle odkaz na článek vybraným lidem
function sendArticle(label, title, image_url, excerpt, url) {
	let message = {
		"messages" : [
			{
				"attachment": {
				"type": "template",
				"payload": {
					"template_type":"generic",
					"elements": [
						{
							"title": title,
							"image_url": image_url,
							"subtitle": excerpt,
							"buttons":[
				            	{
				                	"type":"web_url",
				                	"url": url,
				                	"title": labels.buttons.readmore
				            	}              
				            ],
						}
					],
				},
			}
		}]
	}

	request({
		"uri": config.fb.uri.hostBroadcast + "/me/message_creatives",
		"qs": { "access_token": config.fb.accessToken },
		"method": "POST",
		"json": message
	}, (err, res, body) => {
		if (!err) {
			let creative_id = body.message_creative_id;
			console.log('message: ' + JSON.stringify(message, null, 4));
			console.log('creative id: ' + creative_id);

			let sendCreative = {
				"message_creative_id": creative_id,
				"custom_label_id": label,
				"notification_type": "REGULAR",
				"tag": "NON_PROMOTIONAL_SUBSCRIPTION"
			}

			request({
				"uri": "https://graph.facebook.com/v2.11/me/broadcast_messages",
				"qs": { "access_token": config.fb.accessToken },
				"method": "POST",
				"json": sendCreative
			}, (err, res, body) => {
				if (!err) {
			  		console.log('Nasledující zpráva byla odeslána:' + JSON.stringify(sendCreative, null, 4));
			  		console.log('Body:' + JSON.stringify(body, null, 4));
				} else {
			  		console.error("Došlo k chybě:" + err);
			  		console.log('Body:' + JSON.stringify(body, null, 4));
				}
			});
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
}

module.exports = {
	sendInfo,
	getLabels,
	labelCreate,
	labelSignIn,
	labelSignOut,
	sendArticle
};
