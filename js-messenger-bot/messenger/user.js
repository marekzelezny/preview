'use strict';

const
	request = require('request'),
	config = require('../config');

// GET: Získá info o uživateli
function getUserInfo(psid, callback) {

	request({
		"uri": config.fb.uri.host + psid,
		"qs": { 
			"fields": "first_name,last_name,profile_pic",
			"access_token": config.fb.accessToken 
		},
		"method": "GET"
	}, (err, res, body) => {
		if (!err && res.statusCode == 200) {
	  		callback(body);
		} else {
	  		console.error("Došlo k chybě:" + err);
	  		console.error(body);
		}
	});
};

function userTest() {
	console.log(config.fb.uri.host);
}

module.exports = {
	getUserInfo,
	userTest
};
