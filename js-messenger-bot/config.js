module.exports = {
	fb: {
		uri: {
			host: 'https://graph.facebook.com/v2.6/',
			hostBroadcast: 'https://graph.facebook.com/v2.11/'
		},
		verifyToken: 'xxxx',
		accessToken: 'xxxx',
		accessTokenTest: 'xxxx',
		broadcastToken: 'xxxx'
	},
	labels: {
		pageInboxThread: 'xxxx', // Default Page Inbox ID for every page
		notifikace: 'xxxx',
		notifikaceTest: 'xxxx'
	},
	payload: {
		getStarted: 'get_started',
		returnBack: 'returnBack',
		handover: {
			info: 'handoverInfo',
			passToInbox: 'handoverPassToInbox',
			passToBot: 'handoverPassToBot',
		},
		notifications: {
			messenger: 'messenger',
			signIn: 'messengerSignin',
			signOut: 'messengerSignOut',
		}
	},
	urls: {
		f24: 'http://forum24.cz',
		notifikace: 'http://forum24.cz/notifikace/',
		sbirka: 'http://forum24.cz/prosime-podporte-svobodne-forum/',
		revue: 'http://revueforum.cz',
		newsletterSignUp: 'http://eepurl.com/dhY2HL' 
	}
}
