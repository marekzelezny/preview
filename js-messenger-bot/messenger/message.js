'use strict';

const
	request = require('request'),
	labels = require('../labels'),
	config = require('../config');

// POST: Odešle objekt
function callSendAPI(sender_psid, response) {

	let request_body = {
		"recipient": {
	  		"id": sender_psid
		},
		"message": response
	}

	// Send the HTTP request to the Messenger Platform
	request({
		"uri": config.fb.uri.host + "/me/messages",
		"qs": { "access_token": config.fb.accessToken },
		"method": "POST",
		"json": request_body
	}, (err, res, body) => {
		if (!err) {
	  		console.log('Nasledující zpráva byla odeslána:' + JSON.stringify(request_body, null, 4));
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
};

// POST: Odešle zprávu příjemci
function sendMessage(sender_psid, message, delay) {
	let response = { "text": message }

	if(delay) {
		setTimeout(function() {
		    callSendAPI(sender_psid, response);
		}, delay);
	} else {
		callSendAPI(sender_psid, response);
	}

}

// POST: Odešle URL přijemci
function sendURL(sender_psid, url) {
	let opengraph = {
		"attachment": {
			"type": "template",
			"payload": {
				"template_type":"open_graph",
				"elements": [
					{
						"url": url,
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
	}

	callSendAPI(sender_psid, opengraph);
}

// POST: Odešle obrázek / video
function sendMedia(sender_psid, url, typeOfMedia, callback) {

	let prepareMedia = {
		"message": {
			"attachment": {
				"type": typeOfMedia,
				"payload": {
					"url": url
				}
			}
		}
	}

	request({
		"uri": config.fb.uri.host + "/me/message_attachments",
		"qs": { "access_token": config.fb.accessToken },
		"method": "POST",
		"json": prepareMedia
	}, (err, res, body) => {
		if (!err) {
			let attachment_id = body.attachment_id;
			console.log('body: ' + JSON.stringify(body, null, 4));
			console.log('attachment_id: ' + attachment_id);

			let final = {
				"recipient": {
			  		"id": sender_psid
				},
				"message": {
					"attachment": {
						"type": "template",
						"payload": {
							"template_type":"media",
							"elements": [
								{
									"media_type": typeOfMedia,
									"attachment_id": attachment_id
								}
							],
						},
					}
				}
			}

			request({
				"uri": config.fb.uri.host + "/me/messages",
				"qs": { "access_token": config.fb.accessToken },
				"method": "POST",
				"json": final
			}, (err, res, body) => {
				if (!err) {
			  		console.log('Nasledující zpráva byla odeslána:' + JSON.stringify(final, null, 4));
			  		console.log('Body:' + JSON.stringify(body, null, 4));
			  		callback();
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

function sendQuickReplies(sender_psid, text, data) {
	let message = {
		"text": text,
		"quick_replies": data
	}

	callSendAPI(sender_psid, message);
}

// POST: Odešle senderAction (typing_on, typing_off, mark_seen)
// https://developers.facebook.com/docs/messenger-platform/send-messages/sender-actions
function senderAction(sender_psid, action_type) {
	let request_body = {
		"recipient": {
	  		"id": sender_psid
		},
		"sender_action": action_type
	}

	request({
		"uri": config.fb.uri.host + "/me/messages",
		"qs": { "access_token": config.fb.accessToken },
		"method": "POST",
		"json": action_type
	}, (err, res, body) => {
		if (!err) {
	  		console.log("SenderAction "+action_type+" odeslána úspěšně");
	  		console.log(request_body);
		} else {
	  		console.error("Došlo k chybě při odeslání SenderAction:" + err);
		}
	});
}

module.exports = {
	callSendAPI,
	sendMessage,
	sendQuickReplies,
	sendMedia,
	sendURL,
	senderAction
};
