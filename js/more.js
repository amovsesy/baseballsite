// MooTools: the javascript framework.
// Load this file's selection again by visiting: http://mootools.net/more/ea71e7d3aab3551fe45865dbb054a052 
// Or build this file again with packager using: packager build More/Date More/Drag More/Drag.Move More/IframeShim
/*
---

script: More.js

name: More

description: MooTools More

license: MIT-style license

authors:
  - Guillermo Rauch
  - Thomas Aylott
  - Scott Kyle
  - Arian Stolwijk
  - Tim Wienk
  - Christoph Pojer
  - Aaron Newton

requires:
  - Core/MooTools

provides: [MooTools.More]

...
*/

MooTools.More = {
	'version': '1.3.0.1',
	'build': '6dce99bed2792dffcbbbb4ddc15a1fb9a41994b5'
};


/*
---

script: Class.Binds.js

name: Class.Binds

description: Automagically binds specified methods in a class to the instance of the class.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Class
  - /MooTools.More

provides: [Class.Binds]

...
*/

Class.Mutators.Binds = function(binds){
  return binds;
};

Class.Mutators.initialize = function(initialize){
  return function(){
    Array.from(this.Binds).each(function(name){
      var original = this[name];
      if (original) this[name] = original.bind(this);
    }, this);
    return initialize.apply(this, arguments);
  };
};

/*
---

script: Object.Extras.js

name: Object.Extras

description: Extra Object generics, like getFromPath which allows a path notation to child elements.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Object
  - /MooTools.More

provides: [Object.Extras]

...
*/

(function(){

var defined = function(value){
	return value != null;
};

Object.extend({

	getFromPath: function(source, key){
		var parts = key.split('.');
		for (var i = 0, l = parts.length; i < l; i++){
			if (source.hasOwnProperty(parts[i])) source = source[parts[i]];
			else return null;
		}
		return source;
	},

	cleanValues: function(object, method){
		method = method || defined;
		for (key in object) if (!method(object[key])){
			delete object[key];
		}
		return object;
	},

	erase: function(object, key){
		if (object.hasOwnProperty(key)) delete object[key];
		return object;
	},

	run: function(object){
		var args = Array.slice(arguments, 1);
		for (key in object) if (object[key].apply){
			object[key].apply(object, args);
		}
		return object;
	}

});

})();


/*
---

script: Locale.js

name: Locale

description: Provides methods for localization.

license: MIT-style license

authors:
  - Aaron Newton
  - Arian Stolwijk

requires:
  - Core/Events
  - /Object.Extras
  - /MooTools.More

provides: [Locale, Lang]

...
*/

(function(){

var current = null,
	locales = {},
	inherits = {};

var getSet = function(set){
	if (instanceOf(set, Locale.Set)) return set;
	else return locales[set];
};

var Locale = this.Locale = {

	define: function(locale, set, key, value){
		var name;
		if (instanceOf(locale, Locale.Set)){
			name = locale.name;
			if (name) locales[name] = locale;
		} else {
			name = locale;
			if (!locales[name]) locales[name] = new Locale.Set(name);
			locale = locales[name];
		}

		if (set) locale.define(set, key, value);

		

		if (!current) current = locale;

		return locale;
	},

	use: function(locale){
		locale = getSet(locale);

		if (locale){
			current = locale;

			this.fireEvent('change', locale);

			
		}

		return this;
	},

	getCurrent: function(){
		return current;
	},

	get: function(key, args){
		return (current) ? current.get(key, args) : '';
	},

	inherit: function(locale, inherits, set){
		locale = getSet(locale);

		if (locale) locale.inherit(inherits, set);
		return this;
	},

	list: function(){
		return Object.keys(locales);
	}

};

Object.append(Locale, new Events);

Locale.Set = new Class({

	sets: {},

	inherits: {
		locales: [],
		sets: {}
	},

	initialize: function(name){
		this.name = name || '';
	},

	define: function(set, key, value){
		var defineData = this.sets[set];
		if (!defineData) defineData = {};

		if (key){
			if (typeOf(key) == 'object') defineData = Object.merge(defineData, key);
			else defineData[key] = value;
		}
		this.sets[set] = defineData;

		return this;
	},

	get: function(key, args, _base){
		var value = Object.getFromPath(this.sets, key);
		if (value != null){
			var type = typeOf(value);
			if (type == 'function') value = value.apply(null, Array.from(args));
			else if (type == 'object') value = Object.clone(value);
			return value;
		}

		// get value of inherited locales
		var index = key.indexOf('.'),
			set = index < 0 ? key : key.substr(0, index),
			names = (this.inherits.sets[set] || []).combine(this.inherits.locales).include('en-US');
		if (!_base) _base = [];

		for (var i = 0, l = names.length; i < l; i++){
			if (_base.contains(names[i])) continue;
			_base.include(names[i]);

			var locale = locales[names[i]];
			if (!locale) continue;

			value = locale.get(key, args, _base);
			if (value != null) return value;
		}

		return '';
	},

	inherit: function(names, set){
		names = Array.from(names);

		if (set && !this.inherits.sets[set]) this.inherits.sets[set] = [];

		var l = names.length;
		while (l--) (set ? this.inherits.sets[set] : this.inherits.locales).unshift(names[l]);

		return this;
	}

});



})();


/*
---

name: Locale.en-US.Date

description: Date messages for US English.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - /Locale

provides: [Locale.en-US.Date]

...
*/

Locale.define('en-US', 'Date', {

	months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	months_abbr: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
	days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
	days_abbr: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

	// Culture's date order: MM/DD/YYYY
	dateOrder: ['month', 'date', 'year'],
	shortDate: '%m/%d/%Y',
	shortTime: '%I:%M%p',
	AM: 'AM',
	PM: 'PM',

	// Date.Extras
	ordinal: function(dayOfMonth){
		// 1st, 2nd, 3rd, etc.
		return (dayOfMonth > 3 && dayOfMonth < 21) ? 'th' : ['th', 'st', 'nd', 'rd', 'th'][Math.min(dayOfMonth % 10, 4)];
	},

	lessThanMinuteAgo: 'less than a minute ago',
	minuteAgo: 'about a minute ago',
	minutesAgo: '{delta} minutes ago',
	hourAgo: 'about an hour ago',
	hoursAgo: 'about {delta} hours ago',
	dayAgo: '1 day ago',
	daysAgo: '{delta} days ago',
	weekAgo: '1 week ago',
	weeksAgo: '{delta} weeks ago',
	monthAgo: '1 month ago',
	monthsAgo: '{delta} months ago',
	yearAgo: '1 year ago',
	yearsAgo: '{delta} years ago',

	lessThanMinuteUntil: 'less than a minute from now',
	minuteUntil: 'about a minute from now',
	minutesUntil: '{delta} minutes from now',
	hourUntil: 'about an hour from now',
	hoursUntil: 'about {delta} hours from now',
	dayUntil: '1 day from now',
	daysUntil: '{delta} days from now',
	weekUntil: '1 week from now',
	weeksUntil: '{delta} weeks from now',
	monthUntil: '1 month from now',
	monthsUntil: '{delta} months from now',
	yearUntil: '1 year from now',
	yearsUntil: '{delta} years from now'

});


/*
---

script: Date.js

name: Date

description: Extends the Date native object to include methods useful in managing dates.

license: MIT-style license

authors:
  - Aaron Newton
  - Nicholas Barthelemy - https://svn.nbarthelemy.com/date-js/
  - Harald Kirshner - mail [at] digitarald.de; http://digitarald.de
  - Scott Kyle - scott [at] appden.com; http://appden.com

requires:
  - Core/Array
  - Core/String
  - Core/Number
  - /Locale
  - /Locale.en-US.Date
  - /MooTools.More

provides: [Date]

...
*/

(function(){

var Date = this.Date;

Date.Methods = {
	ms: 'Milliseconds',
	year: 'FullYear',
	min: 'Minutes',
	mo: 'Month',
	sec: 'Seconds',
	hr: 'Hours'
};

['Date', 'Day', 'FullYear', 'Hours', 'Milliseconds', 'Minutes', 'Month', 'Seconds', 'Time', 'TimezoneOffset',
	'Week', 'Timezone', 'GMTOffset', 'DayOfYear', 'LastMonth', 'LastDayOfMonth', 'UTCDate', 'UTCDay', 'UTCFullYear',
	'AMPM', 'Ordinal', 'UTCHours', 'UTCMilliseconds', 'UTCMinutes', 'UTCMonth', 'UTCSeconds', 'UTCMilliseconds'].each(function(method){
	Date.Methods[method.toLowerCase()] = method;
});

var pad = function(what, length, string){
	if (!string) string = '0';
	return new Array(length - String(what).length + 1).join(string) + what;
};

Date.implement({

	set: function(prop, value){
		prop = prop.toLowerCase();
		var m = Date.Methods;
		if (m[prop]) this['set' + m[prop]](value);
		return this;
	}.overloadSetter(),

	get: function(prop){
		prop = prop.toLowerCase();
		var m = Date.Methods;
		if (m[prop]) return this['get' + m[prop]]();
		return null;
	},

	clone: function(){
		return new Date(this.get('time'));
	},

	increment: function(interval, times){
		interval = interval || 'day';
		times = times != null ? times : 1;

		switch (interval){
			case 'year':
				return this.increment('month', times * 12);
			case 'month':
				var d = this.get('date');
				this.set('date', 1).set('mo', this.get('mo') + times);
				return this.set('date', d.min(this.get('lastdayofmonth')));
			case 'week':
				return this.increment('day', times * 7);
			case 'day':
				return this.set('date', this.get('date') + times);
		}

		if (!Date.units[interval]) throw new Error(interval + ' is not a supported interval');

		return this.set('time', this.get('time') + times * Date.units[interval]());
	},

	decrement: function(interval, times){
		return this.increment(interval, -1 * (times != null ? times : 1));
	},

	isLeapYear: function(){
		return Date.isLeapYear(this.get('year'));
	},

	clearTime: function(){
		return this.set({hr: 0, min: 0, sec: 0, ms: 0});
	},

	diff: function(date, resolution){
		if (typeOf(date) == 'string') date = Date.parse(date);

		return ((date - this) / Date.units[resolution || 'day'](3, 3)).round(); // non-leap year, 30-day month
	},

	getLastDayOfMonth: function(){
		return Date.daysInMonth(this.get('mo'), this.get('year'));
	},

	getDayOfYear: function(){
		return (Date.UTC(this.get('year'), this.get('mo'), this.get('date') + 1)
			- Date.UTC(this.get('year'), 0, 1)) / Date.units.day();
	},

	getWeek: function(){
		return (this.get('dayofyear') / 7).ceil();
	},

	getOrdinal: function(day){
		return Date.getMsg('ordinal', day || this.get('date'));
	},

	getTimezone: function(){
		return this.toString()
			.replace(/^.*? ([A-Z]{3}).[0-9]{4}.*$/, '$1')
			.replace(/^.*?\(([A-Z])[a-z]+ ([A-Z])[a-z]+ ([A-Z])[a-z]+\)$/, '$1$2$3');
	},

	getGMTOffset: function(){
		var off = this.get('timezoneOffset');
		return ((off > 0) ? '-' : '+') + pad((off.abs() / 60).floor(), 2) + pad(off % 60, 2);
	},

	setAMPM: function(ampm){
		ampm = ampm.toUpperCase();
		var hr = this.get('hr');
		if (hr > 11 && ampm == 'AM') return this.decrement('hour', 12);
		else if (hr < 12 && ampm == 'PM') return this.increment('hour', 12);
		return this;
	},

	getAMPM: function(){
		return (this.get('hr') < 12) ? 'AM' : 'PM';
	},

	parse: function(str){
		this.set('time', Date.parse(str));
		return this;
	},

	isValid: function(date){
		return !isNaN((date || this).valueOf());
	},

	format: function(f){
		if (!this.isValid()) return 'invalid date';
		f = f || '%x %X';
		f = formats[f.toLowerCase()] || f; // replace short-hand with actual format
		var d = this;
		return f.replace(/%([a-z%])/gi,
			function($0, $1){
				switch ($1){
					case 'a': return Date.getMsg('days_abbr')[d.get('day')];
					case 'A': return Date.getMsg('days')[d.get('day')];
					case 'b': return Date.getMsg('months_abbr')[d.get('month')];
					case 'B': return Date.getMsg('months')[d.get('month')];
					case 'c': return d.format('%a %b %d %H:%m:%S %Y');
					case 'd': return pad(d.get('date'), 2);
					case 'e': return pad(d.get('date'), 2, ' ');
					case 'H': return pad(d.get('hr'), 2);
					case 'I': return pad((d.get('hr') % 12) || 12, 2);
					case 'j': return pad(d.get('dayofyear'), 3);
					case 'k': return pad(d.get('hr'), 2, ' ');
					case 'l': return pad((d.get('hr') % 12) || 12, 2, ' ');
					case 'L': return pad(d.get('ms'), 3);
					case 'm': return pad((d.get('mo') + 1), 2);
					case 'M': return pad(d.get('min'), 2);
					case 'o': return d.get('ordinal');
					case 'p': return Date.getMsg(d.get('ampm'));
					case 's': return Math.round(d / 1000);
					case 'S': return pad(d.get('seconds'), 2);
					case 'U': return pad(d.get('week'), 2);
					case 'w': return d.get('day');
					case 'x': return d.format(Date.getMsg('shortDate'));
					case 'X': return d.format(Date.getMsg('shortTime'));
					case 'y': return d.get('year').toString().substr(2);
					case 'Y': return d.get('year');
					
					case 'z': return d.get('GMTOffset');
					case 'Z': return d.get('Timezone');
				}
				return $1;
			}
		);
	},

	toISOString: function(){
		return this.format('iso8601');
	}

});


Date.alias('toJSON', 'toISOString');
Date.alias('compare', 'diff');
Date.alias('strftime', 'format');

var formats = {
	db: '%Y-%m-%d %H:%M:%S',
	compact: '%Y%m%dT%H%M%S',
	iso8601: '%Y-%m-%dT%H:%M:%S%T',
	rfc822: '%a, %d %b %Y %H:%M:%S %Z',
	'short': '%d %b %H:%M',
	'long': '%B %d, %Y %H:%M'
};

var parsePatterns = [];
var nativeParse = Date.parse;

var parseWord = function(type, word, num){
	var ret = -1;
	var translated = Date.getMsg(type + 's');
	switch (typeOf(word)){
		case 'object':
			ret = translated[word.get(type)];
			break;
		case 'number':
			ret = translated[word];
			if (!ret) throw new Error('Invalid ' + type + ' index: ' + word);
			break;
		case 'string':
			var match = translated.filter(function(name){
				return this.test(name);
			}, new RegExp('^' + word, 'i'));
			if (!match.length) throw new Error('Invalid ' + type + ' string');
			if (match.length > 1) throw new Error('Ambiguous ' + type);
			ret = match[0];
	}

	return (num) ? translated.indexOf(ret) : ret;
};

Date.extend({

	getMsg: function(key, args){
		return Locale.get('Date.' + key, args);
	},

	units: {
		ms: Function.from(1),
		second: Function.from(1000),
		minute: Function.from(60000),
		hour: Function.from(3600000),
		day: Function.from(86400000),
		week: Function.from(608400000),
		month: function(month, year){
			var d = new Date;
			return Date.daysInMonth(month != null ? month : d.get('mo'), year != null ? year : d.get('year')) * 86400000;
		},
		year: function(year){
			year = year || new Date().get('year');
			return Date.isLeapYear(year) ? 31622400000 : 31536000000;
		}
	},

	daysInMonth: function(month, year){
		return [31, Date.isLeapYear(year) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
	},

	isLeapYear: function(year){
		return ((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0);
	},

	parse: function(from){
		var t = typeOf(from);
		if (t == 'number') return new Date(from);
		if (t != 'string') return from;
		from = from.clean();
		if (!from.length) return null;

		var parsed;
		parsePatterns.some(function(pattern){
			var bits = pattern.re.exec(from);
			return (bits) ? (parsed = pattern.handler(bits)) : false;
		});
		return parsed || new Date(nativeParse(from));
	},

	parseDay: function(day, num){
		return parseWord('day', day, num);
	},

	parseMonth: function(month, num){
		return parseWord('month', month, num);
	},

	parseUTC: function(value){
		var localDate = new Date(value);
		var utcSeconds = Date.UTC(
			localDate.get('year'),
			localDate.get('mo'),
			localDate.get('date'),
			localDate.get('hr'),
			localDate.get('min'),
			localDate.get('sec'),
			localDate.get('ms')
		);
		return new Date(utcSeconds);
	},

	orderIndex: function(unit){
		return Date.getMsg('dateOrder').indexOf(unit) + 1;
	},

	defineFormat: function(name, format){
		formats[name] = format;
	},

	defineFormats: function(formats){
		for (var name in formats) Date.defineFormat(name, formats[name]);
	},



	defineParser: function(pattern){
		parsePatterns.push((pattern.re && pattern.handler) ? pattern : build(pattern));
	},

	defineParsers: function(){
		Array.flatten(arguments).each(Date.defineParser);
	},

	define2DigitYearStart: function(year){
		startYear = year % 100;
		startCentury = year - startYear;
	}

});

var startCentury = 1900;
var startYear = 70;

var regexOf = function(type){
	return new RegExp('(?:' + Date.getMsg(type).map(function(name){
		return name.substr(0, 3);
	}).join('|') + ')[a-z]*');
};

var replacers = function(key){
	switch(key){
		case 'x': // iso8601 covers yyyy-mm-dd, so just check if month is first
			return ((Date.orderIndex('month') == 1) ? '%m[-./]%d' : '%d[-./]%m') + '([-./]%y)?';
		case 'X':
			return '%H([.:]%M)?([.:]%S([.:]%s)?)? ?%p? ?%T?';
	}
	return null;
};

var keys = {
	d: /[0-2]?[0-9]|3[01]/,
	H: /[01]?[0-9]|2[0-3]/,
	I: /0?[1-9]|1[0-2]/,
	M: /[0-5]?\d/,
	s: /\d+/,
	o: /[a-z]*/,
	p: /[ap]\.?m\.?/,
	y: /\d{2}|\d{4}/,
	Y: /\d{4}/,
	T: /Z|[+-]\d{2}(?::?\d{2})?/
};

keys.m = keys.I;
keys.S = keys.M;

var currentLanguage;

var recompile = function(language){
	currentLanguage = language;

	keys.a = keys.A = regexOf('days');
	keys.b = keys.B = regexOf('months');

	parsePatterns.each(function(pattern, i){
		if (pattern.format) parsePatterns[i] = build(pattern.format);
	});
};

var build = function(format){
	if (!currentLanguage) return {format: format};

	var parsed = [];
	var re = (format.source || format) // allow format to be regex
	 .replace(/%([a-z])/gi,
		function($0, $1){
			return replacers($1) || $0;
		}
	).replace(/\((?!\?)/g, '(?:') // make all groups non-capturing
	 .replace(/ (?!\?|\*)/g, ',? ') // be forgiving with spaces and commas
	 .replace(/%([a-z%])/gi,
		function($0, $1){
			var p = keys[$1];
			if (!p) return $1;
			parsed.push($1);
			return '(' + p.source + ')';
		}
	).replace(/\[a-z\]/gi, '[a-z\\u00c0-\\uffff;\&]'); // handle unicode words

	return {
		format: format,
		re: new RegExp('^' + re + '$', 'i'),
		handler: function(bits){
			bits = bits.slice(1).associate(parsed);
			var date = new Date().clearTime(),
				year = bits.y || bits.Y;

			if (year != null) handle.call(date, 'y', year); // need to start in the right year
			if ('d' in bits) handle.call(date, 'd', 1);
			if ('m' in bits || 'b' in bits || 'B' in bits) handle.call(date, 'm', 1);

			for (var key in bits) handle.call(date, key, bits[key]);
			return date;
		}
	};
};

var handle = function(key, value){
	if (!value) return this;

	switch(key){
		case 'a': case 'A': return this.set('day', Date.parseDay(value, true));
		case 'b': case 'B': return this.set('mo', Date.parseMonth(value, true));
		case 'd': return this.set('date', value);
		case 'H': case 'I': return this.set('hr', value);
		case 'm': return this.set('mo', value - 1);
		case 'M': return this.set('min', value);
		case 'p': return this.set('ampm', value.replace(/\./g, ''));
		case 'S': return this.set('sec', value);
		case 's': return this.set('ms', ('0.' + value) * 1000);
		case 'w': return this.set('day', value);
		case 'Y': return this.set('year', value);
		case 'y':
			value = +value;
			if (value < 100) value += startCentury + (value < startYear ? 100 : 0);
			return this.set('year', value);
		case 'T':
			if (value == 'Z') value = '+00';
			var offset = value.match(/([+-])(\d{2}):?(\d{2})?/);
			offset = (offset[1] + '1') * (offset[2] * 60 + (+offset[3] || 0)) + this.getTimezoneOffset();
			return this.set('time', this - offset * 60000);
	}

	return this;
};

Date.defineParsers(
	'%Y([-./]%m([-./]%d((T| )%X)?)?)?', // "1999-12-31", "1999-12-31 11:59pm", "1999-12-31 23:59:59", ISO8601
	'%Y%m%d(T%H(%M%S?)?)?', // "19991231", "19991231T1159", compact
	'%x( %X)?', // "12/31", "12.31.99", "12-31-1999", "12/31/2008 11:59 PM"
	'%d%o( %b( %Y)?)?( %X)?', // "31st", "31st December", "31 Dec 1999", "31 Dec 1999 11:59pm"
	'%b( %d%o)?( %Y)?( %X)?', // Same as above with month and day switched
	'%Y %b( %d%o( %X)?)?', // Same as above with year coming first
	'%o %b %d %X %T %Y' // "Thu Oct 22 08:11:23 +0000 2009"
);

Locale.addEvent('change', function(language){
	if (Locale.get('Date')) recompile(language);
}).fireEvent('change', Locale.getCurrent());

})();

/*
---

script: Element.Measure.js

name: Element.Measure

description: Extends the Element native object to include methods useful in measuring dimensions.

credits: "Element.measure / .expose methods by Daniel Steigerwald License: MIT-style license. Copyright: Copyright (c) 2008 Daniel Steigerwald, daniel.steigerwald.cz"

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Element.Style
  - Core/Element.Dimensions
  - /MooTools.More

provides: [Element.Measure]

...
*/

(function(){

var getStylesList = function(styles, planes){
	var list = [];
	Object.each(planes, function(directions){
		Object.each(directions, function(edge){
			styles.each(function(style){
				list.push(style + '-' + edge + (style == 'border' ? '-width' : ''));
			});
		});
	});
	return list;
};

var calculateEdgeSize = function(edge, styles){
	var total = 0;
	Object.each(styles, function(value, style){
		if (style.test(edge)) total = total + value.toInt();
	});
	return total;
};


Element.implement({

	measure: function(fn){
		var visibility = function(el){
			return !!(!el || el.offsetHeight || el.offsetWidth);
		};
		if (visibility(this)) return fn.apply(this);
		var parent = this.getParent(),
			restorers = [],
			toMeasure = [];
		while (!visibility(parent) && parent != document.body){
			toMeasure.push(parent.expose());
			parent = parent.getParent();
		}
		var restore = this.expose();
		var result = fn.apply(this);
		restore();
		toMeasure.each(function(restore){
			restore();
		});
		return result;
	},

	expose: function(){
		if (this.getStyle('display') != 'none') return function(){};
		var before = this.style.cssText;
		this.setStyles({
			display: 'block',
			position: 'absolute',
			visibility: 'hidden'
		});
		return function(){
			this.style.cssText = before;
		}.bind(this);
	},

	getDimensions: function(options){
		options = Object.merge({computeSize: false}, options);
		var dim = {x: 0, y: 0};

		var getSize = function(el, options){
			return (options.computeSize) ? el.getComputedSize(options) : el.getSize();
		};

		var parent = this.getParent('body');

		if (parent && this.getStyle('display') == 'none'){
			dim = this.measure(function(){
				return getSize(this, options);
			});
		} else if (parent){
			try { //safari sometimes crashes here, so catch it
				dim = getSize(this, options);
			}catch(e){}
		}

		return Object.append(dim, (dim.x || dim.x === 0) ? {
				width: dim.x,
				height: dim.y
			} : {
				x: dim.width,
				y: dim.height
			}
		);
	},

	getComputedSize: function(options){
		

		options = Object.merge({
			styles: ['padding','border'],
			planes: {
				height: ['top','bottom'],
				width: ['left','right']
			},
			mode: 'both'
		}, options);

		var styles = {},
			size = {width: 0, height: 0};

		if (options.mode == 'vertical'){
			delete size.width;
			delete options.planes.width;
		} else if (options.mode == 'horizontal'){
			delete size.height;
			delete options.planes.height;
		}


		getStylesList(options.styles, options.planes).each(function(style){
			styles[style] = this.getStyle(style).toInt();
		}, this);

		Object.each(options.planes, function(edges, plane){

			var capitalized = plane.capitalize();
			styles[plane] = this.getStyle(plane).toInt();
			size['total' + capitalized] = styles[plane];

			edges.each(function(edge){
				var edgesize = calculateEdgeSize(edge, styles);
				size['computed' + edge.capitalize()] = edgesize;
				size['total' + capitalized] += edgesize;
			});

		}, this);

		return Object.append(size, styles);
	}

});

})();


/*
---

script: Element.Position.js

name: Element.Position

description: Extends the Element native object to include methods useful positioning elements relative to others.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Element.Dimensions
  - /Element.Measure

provides: [Element.Position]

...
*/

(function(){

var original = Element.prototype.position;

Element.implement({

	position: function(options){
		//call original position if the options are x/y values
		if (options && (options.x != null || options.y != null)){
			return original ? original.apply(this, arguments) : this;
		}

		Object.each(options || {}, function(v, k){
			if (v == null) delete options[k];
		});

		options = Object.merge({
			// minimum: { x: 0, y: 0 },
			// maximum: { x: 0, y: 0},
			relativeTo: document.body,
			position: {
				x: 'center', //left, center, right
				y: 'center' //top, center, bottom
			},
			offset: {x: 0, y: 0}/*,
			edge: false,
			returnPos: false,
			relFixedPosition: false,
			ignoreMargins: false,
			ignoreScroll: false,
			allowNegative: false*/
		}, options);

		//compute the offset of the parent positioned element if this element is in one
		var parentOffset = {x: 0, y: 0},
			parentPositioned = false;

		/* dollar around getOffsetParent should not be necessary, but as it does not return
		 * a mootools extended element in IE, an error occurs on the call to expose. See:
		 * http://mootools.lighthouseapp.com/projects/2706/tickets/333-element-getoffsetparent-inconsistency-between-ie-and-other-browsers */
		var offsetParent = this.measure(function(){
			return document.id(this.getOffsetParent());
		});
		if (offsetParent && offsetParent != this.getDocument().body){
			parentOffset = offsetParent.measure(function(){
				return this.getPosition();
			});
			parentPositioned = offsetParent != document.id(options.relativeTo);
			options.offset.x = options.offset.x - parentOffset.x;
			options.offset.y = options.offset.y - parentOffset.y;
		}

		//upperRight, bottomRight, centerRight, upperLeft, bottomLeft, centerLeft
		//topRight, topLeft, centerTop, centerBottom, center
		var fixValue = function(option){
			if (typeOf(option) != 'string') return option;
			option = option.toLowerCase();
			var val = {};

			if (option.test('left')){
				val.x = 'left';
			} else if (option.test('right')){
				val.x = 'right';
			} else {
				val.x = 'center';
			}

			if (option.test('upper') || option.test('top')){
				val.y = 'top';
			} else if (option.test('bottom')){
				val.y = 'bottom';
			} else {
				val.y = 'center';
			}

			return val;
		};

		options.edge = fixValue(options.edge);
		options.position = fixValue(options.position);
		if (!options.edge){
			if (options.position.x == 'center' && options.position.y == 'center') options.edge = {x:'center', y:'center'};
			else options.edge = {x:'left', y:'top'};
		}

		this.setStyle('position', 'absolute');
		var rel = document.id(options.relativeTo) || document.body,
				calc = rel == document.body ? window.getScroll() : rel.getPosition(),
				top = calc.y, left = calc.x;

		var dim = this.getDimensions({
			computeSize: true,
			styles:['padding', 'border','margin']
		});

		var pos = {},
			prefY = options.offset.y,
			prefX = options.offset.x,
			winSize = window.getSize();

		switch(options.position.x){
			case 'left':
				pos.x = left + prefX;
				break;
			case 'right':
				pos.x = left + prefX + rel.offsetWidth;
				break;
			default: //center
				pos.x = left + ((rel == document.body ? winSize.x : rel.offsetWidth)/2) + prefX;
				break;
		}

		switch(options.position.y){
			case 'top':
				pos.y = top + prefY;
				break;
			case 'bottom':
				pos.y = top + prefY + rel.offsetHeight;
				break;
			default: //center
				pos.y = top + ((rel == document.body ? winSize.y : rel.offsetHeight)/2) + prefY;
				break;
		}

		if (options.edge){
			var edgeOffset = {};

			switch(options.edge.x){
				case 'left':
					edgeOffset.x = 0;
					break;
				case 'right':
					edgeOffset.x = -dim.x-dim.computedRight-dim.computedLeft;
					break;
				default: //center
					edgeOffset.x = -(dim.totalWidth/2);
					break;
			}

			switch(options.edge.y){
				case 'top':
					edgeOffset.y = 0;
					break;
				case 'bottom':
					edgeOffset.y = -dim.y-dim.computedTop-dim.computedBottom;
					break;
				default: //center
					edgeOffset.y = -(dim.totalHeight/2);
					break;
			}

			pos.x += edgeOffset.x;
			pos.y += edgeOffset.y;
		}

		pos = {
			left: ((pos.x >= 0 || parentPositioned || options.allowNegative) ? pos.x : 0).toInt(),
			top: ((pos.y >= 0 || parentPositioned || options.allowNegative) ? pos.y : 0).toInt()
		};

		var xy = {left: 'x', top: 'y'};

		['minimum', 'maximum'].each(function(minmax){
			['left', 'top'].each(function(lr){
				var val = options[minmax] ? options[minmax][xy[lr]] : null;
				if (val != null && ((minmax == 'minimum') ? pos[lr] < val : pos[lr] > val)) pos[lr] = val;
			});
		});

		if (rel.getStyle('position') == 'fixed' || options.relFixedPosition){
			var winScroll = window.getScroll();
			pos.top+= winScroll.y;
			pos.left+= winScroll.x;
		}
		if (options.ignoreScroll){
			var relScroll = rel.getScroll();
			pos.top -= relScroll.y;
			pos.left -= relScroll.x;
		}

		if (options.ignoreMargins){
			pos.left += (
				options.edge.x == 'right' ? dim['margin-right'] :
				options.edge.x == 'center' ? -dim['margin-left'] + ((dim['margin-right'] + dim['margin-left'])/2) :
					- dim['margin-left']
			);
			pos.top += (
				options.edge.y == 'bottom' ? dim['margin-bottom'] :
				options.edge.y == 'center' ? -dim['margin-top'] + ((dim['margin-bottom'] + dim['margin-top'])/2) :
					- dim['margin-top']
			);
		}

		pos.left = Math.ceil(pos.left);
		pos.top = Math.ceil(pos.top);
		if (options.returnPos) return pos;
		else this.setStyles(pos);
		return this;
	}

});

})();


/*
---

script: Class.Occlude.js

name: Class.Occlude

description: Prevents a class from being applied to a DOM element twice.

license: MIT-style license.

authors:
  - Aaron Newton

requires:
  - Core/Class
  - Core/Element
  - /MooTools.More

provides: [Class.Occlude]

...
*/

Class.Occlude = new Class({

	occlude: function(property, element){
		element = document.id(element || this.element);
		var instance = element.retrieve(property || this.property);
		if (instance && this.occluded != null)
			return this.occluded = instance;

		this.occluded = false;
		element.store(property || this.property, this);
		return this.occluded;
	}

});


/*
---

script: IframeShim.js

name: IframeShim

description: Defines IframeShim, a class for obscuring select lists and flash objects in IE.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Element.Event
  - Core/Element.Style
  - Core/Options
  - Core/Events
  - /Element.Position
  - /Class.Occlude

provides: [IframeShim]

...
*/

var IframeShim = new Class({

	Implements: [Options, Events, Class.Occlude],

	options: {
		className: 'iframeShim',
		src: 'javascript:false;document.write("");',
		display: false,
		zIndex: null,
		margin: 0,
		offset: {x: 0, y: 0},
		browsers: ((Browser.ie && Browser.version == 6) || (Browser.firefox && Browser.version < 3 && Browser.Platform.mac))
	},

	property: 'IframeShim',

	initialize: function(element, options){
		this.element = document.id(element);
		if (this.occlude()) return this.occluded;
		this.setOptions(options);
		this.makeShim();
		return this;
	},

	makeShim: function(){
		if (this.options.browsers){
			var zIndex = this.element.getStyle('zIndex').toInt();

			if (!zIndex){
				zIndex = 1;
				var pos = this.element.getStyle('position');
				if (pos == 'static' || !pos) this.element.setStyle('position', 'relative');
				this.element.setStyle('zIndex', zIndex);
			}
			zIndex = ((this.options.zIndex != null || this.options.zIndex === 0) && zIndex > this.options.zIndex) ? this.options.zIndex : zIndex - 1;
			if (zIndex < 0) zIndex = 1;
			this.shim = new Element('iframe', {
				src: this.options.src,
				scrolling: 'no',
				frameborder: 0,
				styles: {
					zIndex: zIndex,
					position: 'absolute',
					border: 'none',
					filter: 'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)'
				},
				'class': this.options.className
			}).store('IframeShim', this);
			var inject = (function(){
				this.shim.inject(this.element, 'after');
				this[this.options.display ? 'show' : 'hide']();
				this.fireEvent('inject');
			}).bind(this);
			if (!IframeShim.ready) window.addEvent('load', inject);
			else inject();
		} else {
			this.position = this.hide = this.show = this.dispose = Function.from(this);
		}
	},

	position: function(){
		if (!IframeShim.ready || !this.shim) return this;
		var size = this.element.measure(function(){
			return this.getSize();
		});
		if (this.options.margin != undefined){
			size.x = size.x - (this.options.margin * 2);
			size.y = size.y - (this.options.margin * 2);
			this.options.offset.x += this.options.margin;
			this.options.offset.y += this.options.margin;
		}
		this.shim.set({width: size.x, height: size.y}).position({
			relativeTo: this.element,
			offset: this.options.offset
		});
		return this;
	},

	hide: function(){
		if (this.shim) this.shim.setStyle('display', 'none');
		return this;
	},

	show: function(){
		if (this.shim) this.shim.setStyle('display', 'block');
		return this.position();
	},

	dispose: function(){
		if (this.shim) this.shim.dispose();
		return this;
	},

	destroy: function(){
		if (this.shim) this.shim.destroy();
		return this;
	}

});

window.addEvent('load', function(){
	IframeShim.ready = true;
});

Fx.Slide = new Class({

  Extends: Fx,

  options: {
    mode: 'vertical'
  },

  initialize: function(element, options){
    this.addEvent('complete', function(){
      this.open = (this.wrapper['offset' + this.layout.capitalize()] != 0);
      if (this.open && Browser.Engine.webkit419) this.element.dispose().inject(this.wrapper);
    }, true);
    this.element = this.subject = $(element);
    this.parent(options);
    var wrapper = this.element.retrieve('wrapper');
    this.wrapper = wrapper || new Element('div', {
      styles: $extend(this.element.getStyles('margin', 'position'), {'overflow': 'hidden'})
    }).wraps(this.element);
    this.element.store('wrapper', this.wrapper).setStyle('margin', 0);
    this.now = [];
    this.open = true;
  },

  vertical: function(){
    this.margin = 'margin-top';
    this.layout = 'height';
    this.offset = this.element.offsetHeight;
  },

  horizontal: function(){
    this.margin = 'margin-left';
    this.layout = 'width';
    this.offset = this.element.offsetWidth;
  },

  set: function(now){
    this.element.setStyle(this.margin, now[0]);
    this.wrapper.setStyle(this.layout, now[1]);
    return this;
  },

  compute: function(from, to, delta){
    var now = [];
    var x = 2;
    x.times(function(i){
      now[i] = Fx.compute(from[i], to[i], delta);
    });
    return now;
  },

  start: function(how, mode){
    if (!this.check(arguments.callee, how, mode)) return this;
    this[mode || this.options.mode]();
    var margin = this.element.getStyle(this.margin).toInt();
    var layout = this.wrapper.getStyle(this.layout).toInt();
    var caseIn = [[margin, layout], [0, this.offset]];
    var caseOut = [[margin, layout], [-this.offset, 0]];
    var start;
    switch (how){
      case 'in': start = caseIn; break;
      case 'out': start = caseOut; break;
      case 'toggle': start = (this.wrapper['offset' + this.layout.capitalize()] == 0) ? caseIn : caseOut;
    }
    return this.parent(start[0], start[1]);
  },

  slideIn: function(mode){
    return this.start('in', mode);
  },

  slideOut: function(mode){
    return this.start('out', mode);
  },

  hide: function(mode){
    this[mode || this.options.mode]();
    this.open = false;
    return this.set([-this.offset, 0]);
  },

  show: function(mode){
    this[mode || this.options.mode]();
    this.open = true;
    return this.set([0, this.offset]);
  },

  toggle: function(mode){
    return this.start('toggle', mode);
  }

});

Element.Properties.slide = {

  set: function(options){
    var slide = this.retrieve('slide');
    if (slide) slide.cancel();
    return this.eliminate('slide').store('slide:options', $extend({link: 'cancel'}, options));
  },
  
  get: function(options){
    if (options || !this.retrieve('slide')){
      if (options || !this.retrieve('slide:options')) this.set('slide', options);
      this.store('slide', new Fx.Slide(this, this.retrieve('slide:options')));
    }
    return this.retrieve('slide');
  }

};

Element.implement({

  slide: function(how, mode){
    how = how || 'toggle';
    var slide = this.get('slide'), toggle;
    switch (how){
      case 'hide': slide.hide(mode); break;
      case 'show': slide.show(mode); break;
      case 'toggle':
        var flag = this.retrieve('slide:flag', slide.open);
        slide[(flag) ? 'slideOut' : 'slideIn'](mode);
        this.store('slide:flag', !flag);
        toggle = true;
      break;
      default: slide.start(how, mode);
    }
    if (!toggle) this.eliminate('slide:flag');
    return this;
  }

});


/*
Script: Fx.Scroll.js
  Effect to smoothly scroll any element, including the window.

License:
  MIT-style license.
*/

Fx.Scroll = new Class({

  Extends: Fx,

  options: {
    offset: {'x': 0, 'y': 0},
    wheelStops: true
  },

  initialize: function(element, options){
    this.element = this.subject = $(element);
    this.parent(options);
    var cancel = this.cancel.bind(this, false);

    if ($type(this.element) != 'element') this.element = $(this.element.getDocument().body);

    var stopper = this.element;

    if (this.options.wheelStops){
      this.addEvent('start', function(){
        stopper.addEvent('mousewheel', cancel);
      }, true);
      this.addEvent('complete', function(){
        stopper.removeEvent('mousewheel', cancel);
      }, true);
    }
  },

  set: function(){
    var now = Array.flatten(arguments);
    this.element.scrollTo(now[0], now[1]);
  },

  compute: function(from, to, delta){
    var now = [];
    var x = 2;
    x.times(function(i){
      now.push(Fx.compute(from[i], to[i], delta));
    });
    return now;
  },

  start: function(x, y){
    if (!this.check(arguments.callee, x, y)) return this;
    var offsetSize = this.element.getSize(), scrollSize = this.element.getScrollSize();
    var scroll = this.element.getScroll(), values = {x: x, y: y};
    for (var z in values){
      var max = scrollSize[z] - offsetSize[z];
      if ($chk(values[z])) values[z] = ($type(values[z]) == 'number') ? values[z].limit(0, max) : max;
      else values[z] = scroll[z];
      values[z] += this.options.offset[z];
    }
    return this.parent([scroll.x, scroll.y], [values.x, values.y]);
  },

  toTop: function(){
    return this.start(false, 0);
  },

  toLeft: function(){
    return this.start(0, false);
  },

  toRight: function(){
    return this.start('right', false);
  },

  toBottom: function(){
    return this.start(false, 'bottom');
  },

  toElement: function(el){
    var position = $(el).getPosition(this.element);
    return this.start(position.x, position.y);
  }

});


/*
Script: Fx.Elements.js
  Effect to change any number of CSS properties of any number of Elements.

License:
  MIT-style license.
*/

Fx.Elements = new Class({

  Extends: Fx.CSS,

  initialize: function(elements, options){
    this.elements = this.subject = $$(elements);
    this.parent(options);
  },

  compute: function(from, to, delta){
    var now = {};
    for (var i in from){
      var iFrom = from[i], iTo = to[i], iNow = now[i] = {};
      for (var p in iFrom) iNow[p] = this.parent(iFrom[p], iTo[p], delta);
    }
    return now;
  },

  set: function(now){
    for (var i in now){
      var iNow = now[i];
      for (var p in iNow) this.render(this.elements[i], p, iNow[p], this.options.unit);
    }
    return this;
  },

  start: function(obj){
    if (!this.check(arguments.callee, obj)) return this;
    var from = {}, to = {};
    for (var i in obj){
      var iProps = obj[i], iFrom = from[i] = {}, iTo = to[i] = {};
      for (var p in iProps){
        var parsed = this.prepare(this.elements[i], p, iProps[p]);
        iFrom[p] = parsed.from;
        iTo[p] = parsed.to;
      }
    }
    return this.parent(from, to);
  }

});

/*
Script: Drag.js
  The base Drag Class. Can be used to drag and resize Elements using mouse events.

License:
  MIT-style license.
*/

var Drag = new Class({

  Implements: [Events, Options],

  options: {/*
    onBeforeStart: $empty,
    onStart: $empty,
    onDrag: $empty,
    onCancel: $empty,
    onComplete: $empty,*/
    snap: 6,
    unit: 'px',
    grid: false,
    style: true,
    limit: false,
    handle: false,
    invert: false,
    preventDefault: false,
    modifiers: {x: 'left', y: 'top'}
  },

  initialize: function(){
    var params = Array.link(arguments, {'options': Object.type, 'element': $defined});
    this.element = $(params.element);
    this.document = this.element.getDocument();
    this.setOptions(params.options || {});
    var htype = $type(this.options.handle);
    this.handles = (htype == 'array' || htype == 'collection') ? $$(this.options.handle) : $(this.options.handle) || this.element;
    this.mouse = {'now': {}, 'pos': {}};
    this.value = {'start': {}, 'now': {}};
    
    this.selection = (Browser.Engine.trident) ? 'selectstart' : 'mousedown';
    
    this.bound = {
      start: this.start.bind(this),
      check: this.check.bind(this),
      drag: this.drag.bind(this),
      stop: this.stop.bind(this),
      cancel: this.cancel.bind(this),
      eventStop: $lambda(false)
    };
    this.attach();
  },

  attach: function(){
    this.handles.addEvent('mousedown', this.bound.start);
    return this;
  },

  detach: function(){
    this.handles.removeEvent('mousedown', this.bound.start);
    return this;
  },

  start: function(event){
    if (this.options.preventDefault) event.preventDefault();
    this.fireEvent('beforeStart', this.element);
    this.mouse.start = event.page;
    var limit = this.options.limit;
    this.limit = {'x': [], 'y': []};
    for (var z in this.options.modifiers){
      if (!this.options.modifiers[z]) continue;
      if (this.options.style) this.value.now[z] = this.element.getStyle(this.options.modifiers[z]).toInt();
      else this.value.now[z] = this.element[this.options.modifiers[z]];
      if (this.options.invert) this.value.now[z] *= -1;
      this.mouse.pos[z] = event.page[z] - this.value.now[z];
      if (limit && limit[z]){
        for (var i = 2; i--; i){
          if ($chk(limit[z][i])) this.limit[z][i] = $lambda(limit[z][i])();
        }
      }
    }
    if ($type(this.options.grid) == 'number') this.options.grid = {'x': this.options.grid, 'y': this.options.grid};
    this.document.addEvents({mousemove: this.bound.check, mouseup: this.bound.cancel});
    this.document.addEvent(this.selection, this.bound.eventStop);
  },

  check: function(event){
    if (this.options.preventDefault) event.preventDefault();
    var distance = Math.round(Math.sqrt(Math.pow(event.page.x - this.mouse.start.x, 2) + Math.pow(event.page.y - this.mouse.start.y, 2)));
    if (distance > this.options.snap){
      this.cancel();
      this.document.addEvents({
        mousemove: this.bound.drag,
        mouseup: this.bound.stop
      });
      this.fireEvent('start', this.element).fireEvent('snap', this.element);
    }
  },

  drag: function(event){
    if (this.options.preventDefault) event.preventDefault();
    this.mouse.now = event.page;
    for (var z in this.options.modifiers){
      if (!this.options.modifiers[z]) continue;
      this.value.now[z] = this.mouse.now[z] - this.mouse.pos[z];
      if (this.options.invert) this.value.now[z] *= -1;
      if (this.options.limit && this.limit[z]){
        if ($chk(this.limit[z][1]) && (this.value.now[z] > this.limit[z][1])){
          this.value.now[z] = this.limit[z][1];
        } else if ($chk(this.limit[z][0]) && (this.value.now[z] < this.limit[z][0])){
          this.value.now[z] = this.limit[z][0];
        }
      }
      if (this.options.grid[z]) this.value.now[z] -= (this.value.now[z] % this.options.grid[z]);
      if (this.options.style) this.element.setStyle(this.options.modifiers[z], this.value.now[z] + this.options.unit);
      else this.element[this.options.modifiers[z]] = this.value.now[z];
    }
    this.fireEvent('drag', this.element);
  },

  cancel: function(event){
    this.document.removeEvent('mousemove', this.bound.check);
    this.document.removeEvent('mouseup', this.bound.cancel);
    if (event){
      this.document.removeEvent(this.selection, this.bound.eventStop);
      this.fireEvent('cancel', this.element);
    }
  },

  stop: function(event){
    this.document.removeEvent(this.selection, this.bound.eventStop);
    this.document.removeEvent('mousemove', this.bound.drag);
    this.document.removeEvent('mouseup', this.bound.stop);
    if (event) this.fireEvent('complete', this.element);
  }

});

Element.implement({
  
  makeResizable: function(options){
    return new Drag(this, $merge({modifiers: {'x': 'width', 'y': 'height'}}, options));
  }

});

/*
Script: Drag.Move.js
  A Drag extension that provides support for the constraining of draggables to containers and droppables.

License:
  MIT-style license.
*/

Drag.Move = new Class({

  Extends: Drag,

  options: {
    droppables: [],
    container: false
  },

  initialize: function(element, options){
    this.parent(element, options);
    this.droppables = $$(this.options.droppables);
    this.container = $(this.options.container);
    if (this.container && $type(this.container) != 'element') this.container = $(this.container.getDocument().body);
    element = this.element;
    
    var current = element.getStyle('position');
    var position = (current != 'static') ? current : 'absolute';
    if (element.getStyle('left') == 'auto' || element.getStyle('top') == 'auto') element.position(element.getPosition(element.offsetParent));
    
    element.setStyle('position', position);
    
    this.addEvent('start', function(){
      this.checkDroppables();
    }, true);
  },

  start: function(event){
    if (this.container){
      var el = this.element, cont = this.container, ccoo = cont.getCoordinates(el.offsetParent), cps = {}, ems = {};

      ['top', 'right', 'bottom', 'left'].each(function(pad){
        cps[pad] = cont.getStyle('padding-' + pad).toInt();
        ems[pad] = el.getStyle('margin-' + pad).toInt();
      }, this);

      var width = el.offsetWidth + ems.left + ems.right, height = el.offsetHeight + ems.top + ems.bottom;
      var x = [ccoo.left + cps.left, ccoo.right - cps.right - width];
      var y = [ccoo.top + cps.top, ccoo.bottom - cps.bottom - height];

      this.options.limit = {x: x, y: y};
    }
    this.parent(event);
  },

  checkAgainst: function(el){
    el = el.getCoordinates();
    var now = this.mouse.now;
    return (now.x > el.left && now.x < el.right && now.y < el.bottom && now.y > el.top);
  },

  checkDroppables: function(){
    var overed = this.droppables.filter(this.checkAgainst, this).getLast();
    if (this.overed != overed){
      if (this.overed) this.fireEvent('leave', [this.element, this.overed]);
      if (overed){
        this.overed = overed;
        this.fireEvent('enter', [this.element, overed]);
      } else {
        this.overed = null;
      }
    }
  },

  drag: function(event){
    this.parent(event);
    if (this.droppables.length) this.checkDroppables();
  },

  stop: function(event){
    this.checkDroppables();
    this.fireEvent('drop', [this.element, this.overed]);
    this.overed = null;
    return this.parent(event);
  }

});

Element.implement({

  makeDraggable: function(options){
    return new Drag.Move(this, options);
  }

});


/*
Script: Hash.Cookie.js
  Class for creating, reading, and deleting Cookies in JSON format.

License:
  MIT-style license.
*/

Hash.Cookie = new Class({

  Extends: Cookie,

  options: {
    autoSave: true
  },

  initialize: function(name, options){
    this.parent(name, options);
    this.load();
  },

  save: function(){
    var value = JSON.encode(this.hash);
    if (!value || value.length > 4096) return false; //cookie would be truncated!
    if (value == '{}') this.dispose();
    else this.write(value);
    return true;
  },

  load: function(){
    this.hash = new Hash(JSON.decode(this.read(), true));
    return this;
  }

});

Hash.Cookie.implement((function(){
  
  var methods = {};
  
  Hash.each(Hash.prototype, function(method, name){
    methods[name] = function(){
      var value = method.apply(this.hash, arguments);
      if (this.options.autoSave) this.save();
      return value;
    };
  });
  
  return methods;
  
})());

/*
Script: Color.js
  Class for creating and manipulating colors in JavaScript. Supports HSB -> RGB Conversions and vice versa.

License:
  MIT-style license.
*/

var Color = new Native({
  
  initialize: function(color, type){
    if (arguments.length >= 3){
      type = "rgb"; color = Array.slice(arguments, 0, 3);
    } else if (typeof color == 'string'){
      if (color.match(/rgb/)) color = color.rgbToHex().hexToRgb(true);
      else if (color.match(/hsb/)) color = color.hsbToRgb();
      else color = color.hexToRgb(true);
    }
    type = type || 'rgb';
    switch (type){
      case 'hsb':
        var old = color;
        color = color.hsbToRgb();
        color.hsb = old;
      break;
      case 'hex': color = color.hexToRgb(true); break;
    }
    color.rgb = color.slice(0, 3);
    color.hsb = color.hsb || color.rgbToHsb();
    color.hex = color.rgbToHex();
    return $extend(color, this);
  }

});

Color.implement({

  mix: function(){
    var colors = Array.slice(arguments);
    var alpha = ($type(colors.getLast()) == 'number') ? colors.pop() : 50;
    var rgb = this.slice();
    colors.each(function(color){
      color = new Color(color);
      for (var i = 0; i < 3; i++) rgb[i] = Math.round((rgb[i] / 100 * (100 - alpha)) + (color[i] / 100 * alpha));
    });
    return new Color(rgb, 'rgb');
  },

  invert: function(){
    return new Color(this.map(function(value){
      return 255 - value;
    }));
  },

  setHue: function(value){
    return new Color([value, this.hsb[1], this.hsb[2]], 'hsb');
  },

  setSaturation: function(percent){
    return new Color([this.hsb[0], percent, this.hsb[2]], 'hsb');
  },

  setBrightness: function(percent){
    return new Color([this.hsb[0], this.hsb[1], percent], 'hsb');
  }

});

function $RGB(r, g, b){
  return new Color([r, g, b], 'rgb');
};

function $HSB(h, s, b){
  return new Color([h, s, b], 'hsb');
};

function $HEX(hex){
  return new Color(hex, 'hex');
};

Array.implement({

  rgbToHsb: function(){
    var red = this[0], green = this[1], blue = this[2];
    var hue, saturation, brightness;
    var max = Math.max(red, green, blue), min = Math.min(red, green, blue);
    var delta = max - min;
    brightness = max / 255;
    saturation = (max != 0) ? delta / max : 0;
    if (saturation == 0){
      hue = 0;
    } else {
      var rr = (max - red) / delta;
      var gr = (max - green) / delta;
      var br = (max - blue) / delta;
      if (red == max) hue = br - gr;
      else if (green == max) hue = 2 + rr - br;
      else hue = 4 + gr - rr;
      hue /= 6;
      if (hue < 0) hue++;
    }
    return [Math.round(hue * 360), Math.round(saturation * 100), Math.round(brightness * 100)];
  },

  hsbToRgb: function(){
    var br = Math.round(this[2] / 100 * 255);
    if (this[1] == 0){
      return [br, br, br];
    } else {
      var hue = this[0] % 360;
      var f = hue % 60;
      var p = Math.round((this[2] * (100 - this[1])) / 10000 * 255);
      var q = Math.round((this[2] * (6000 - this[1] * f)) / 600000 * 255);
      var t = Math.round((this[2] * (6000 - this[1] * (60 - f))) / 600000 * 255);
      switch (Math.floor(hue / 60)){
        case 0: return [br, t, p];
        case 1: return [q, br, p];
        case 2: return [p, br, t];
        case 3: return [p, q, br];
        case 4: return [t, p, br];
        case 5: return [br, p, q];
      }
    }
    return false;
  }

});

String.implement({

  rgbToHsb: function(){
    var rgb = this.match(/\d{1,3}/g);
    return (rgb) ? hsb.rgbToHsb() : null;
  },
  
  hsbToRgb: function(){
    var hsb = this.match(/\d{1,3}/g);
    return (hsb) ? hsb.hsbToRgb() : null;
  }

});


/*
Script: Group.js
  Class for monitoring collections of events

License:
  MIT-style license.
*/

var Group = new Class({

  initialize: function(){
    this.instances = Array.flatten(arguments);
    this.events = {};
    this.checker = {};
  },

  addEvent: function(type, fn){
    this.checker[type] = this.checker[type] || {};
    this.events[type] = this.events[type] || [];
    if (this.events[type].contains(fn)) return false;
    else this.events[type].push(fn);
    this.instances.each(function(instance, i){
      instance.addEvent(type, this.check.bind(this, [type, instance, i]));
    }, this);
    return this;
  },

  check: function(type, instance, i){
    this.checker[type][i] = true;
    var every = this.instances.every(function(current, j){
      return this.checker[type][j] || false;
    }, this);
    if (!every) return;
    this.checker[type] = {};
    this.events[type].each(function(event){
      event.call(this, this.instances, instance);
    }, this);
  }

});


/*
Script: Assets.js
  Provides methods to dynamically load JavaScript, CSS, and Image files into the document.

License:
  MIT-style license.
*/

var Asset = new Hash({

  javascript: function(source, properties){
    properties = $extend({
      onload: $empty,
      document: document,
      check: $lambda(true)
    }, properties);
    
    var script = new Element('script', {'src': source, 'type': 'text/javascript'});
    
    var load = properties.onload.bind(script), check = properties.check, doc = properties.document;
    delete properties.onload; delete properties.check; delete properties.document;
    
    script.addEvents({
      load: load,
      readystatechange: function(){
        if (['loaded', 'complete'].contains(this.readyState)) load();
      }
    }).setProperties(properties);
    
    
    if (Browser.Engine.webkit419) var checker = (function(){
      if (!$try(check)) return;
      $clear(checker);
      load();
    }).periodical(50);
    
    return script.inject(doc.head);
  },

  css: function(source, properties){
    return new Element('link', $merge({
      'rel': 'stylesheet', 'media': 'screen', 'type': 'text/css', 'href': source
    }, properties)).inject(document.head);
  },

  image: function(source, properties){
    properties = $merge({
      'onload': $empty,
      'onabort': $empty,
      'onerror': $empty
    }, properties);
    var image = new Image();
    var element = $(image) || new Element('img');
    ['load', 'abort', 'error'].each(function(name){
      var type = 'on' + name;
      var event = properties[type];
      delete properties[type];
      image[type] = function(){
        if (!image) return;
        if (!element.parentNode){
          element.width = image.width;
          element.height = image.height;
        }
        image = image.onload = image.onabort = image.onerror = null;
        event.delay(1, element, element);
        element.fireEvent(name, element, 1);
      };
    });
    image.src = element.src = source;
    if (image && image.complete) image.onload.delay(1);
    return element.setProperties(properties);
  },

  images: function(sources, options){
    options = $merge({
      onComplete: $empty,
      onProgress: $empty
    }, options);
    if (!sources.push) sources = [sources];
    var images = [];
    var counter = 0;
    sources.each(function(source){
      var img = new Asset.image(source, {
        'onload': function(){
          options.onProgress.call(this, counter, sources.indexOf(source));
          counter++;
          if (counter == sources.length) options.onComplete();
        }
      });
      images.push(img);
    });
    return new Elements(images);
  }

});

/*
Script: Sortables.js
  Class for creating a drag and drop sorting interface for lists of items.

License:
  MIT-style license.
*/

var Sortables = new Class({

  Implements: [Events, Options],

  options: {/*
    onSort: $empty,
    onStart: $empty,
    onComplete: $empty,*/
    snap: 4,
    opacity: 1,
    clone: false,
    revert: false,
    handle: false,
    constrain: false
  },

  initialize: function(lists, options){
    this.setOptions(options);
    this.elements = [];
    this.lists = [];
    this.idle = true;
    
    this.addLists($$($(lists) || lists));
    if (!this.options.clone) this.options.revert = false;
    if (this.options.revert) this.effect = new Fx.Morph(null, $merge({duration: 250, link: 'cancel'}, this.options.revert));
  },

  attach: function(){
    this.addLists(this.lists);
    return this;
  },

  detach: function(){
    this.lists = this.removeLists(this.lists);
    return this;
  },

  addItems: function(){
    Array.flatten(arguments).each(function(element){
      this.elements.push(element);
      var start = element.retrieve('sortables:start', this.start.bindWithEvent(this, element));
      (this.options.handle ? element.getElement(this.options.handle) || element : element).addEvent('mousedown', start);
    }, this);
    return this;
  },

  addLists: function(){
    Array.flatten(arguments).each(function(list){
      this.lists.push(list);
      this.addItems(list.getChildren());
    }, this);
    return this;
  },

  removeItems: function(){
    var elements = [];
    Array.flatten(arguments).each(function(element){
      elements.push(element);
      this.elements.erase(element);
      var start = element.retrieve('sortables:start');
      (this.options.handle ? element.getElement(this.options.handle) || element : element).removeEvent('mousedown', start);
    }, this);
    return $$(elements);
  },

  removeLists: function(){
    var lists = [];
    Array.flatten(arguments).each(function(list){
      lists.push(list);
      this.lists.erase(list);
      this.removeItems(list.getChildren());
    }, this);
    return $$(lists);
  },

  getClone: function(event, element){
    if (!this.options.clone) return new Element('div').inject(document.body);
    if ($type(this.options.clone) == 'function') return this.options.clone.call(this, event, element, this.list);
    return element.clone(true).setStyles({
      'margin': '0px',
      'position': 'absolute',
      'visibility': 'hidden',
      'width': element.getStyle('width')
    }).inject(this.list).position(element.getPosition(element.getOffsetParent()));
  },

  getDroppables: function(){
    var droppables = this.list.getChildren();
    if (!this.options.constrain) droppables = this.lists.concat(droppables).erase(this.list);
    return droppables.erase(this.clone).erase(this.element);
  },

  insert: function(dragging, element){
    var where = 'inside';
    if (this.lists.contains(element)){
      this.list = element;
      this.drag.droppables = this.getDroppables();
    } else {
      where = this.element.getAllPrevious().contains(element) ? 'before' : 'after';
    }
    this.element.inject(element, where);
    this.fireEvent('sort', [this.element, this.clone]);
  },

  start: function(event, element){
    if (!this.idle) return;
    this.idle = false;
    this.element = element;
    this.opacity = element.get('opacity');
    this.list = element.getParent();
    this.clone = this.getClone(event, element);
    
    this.drag = new Drag.Move(this.clone, {
      snap: this.options.snap,
      container: this.options.constrain && this.element.getParent(),
      droppables: this.getDroppables(),
      onSnap: function(){
        event.stop();
        this.clone.setStyle('visibility', 'visible');
        this.element.set('opacity', this.options.opacity || 0);
        this.fireEvent('start', [this.element, this.clone]);
      }.bind(this),
      onEnter: this.insert.bind(this),
      onCancel: this.reset.bind(this),
      onComplete: this.end.bind(this)
    });
    
    this.clone.inject(this.element, 'before');
    this.drag.start(event);
  },

  end: function(){
    this.drag.detach();
    this.element.set('opacity', this.opacity);
    if (this.effect){
      var dim = this.element.getStyles('width', 'height');
      var pos = this.clone.computePosition(this.element.getPosition(this.clone.offsetParent));
      this.effect.element = this.clone;
      this.effect.start({
        top: pos.top,
        left: pos.left,
        width: dim.width,
        height: dim.height,
        opacity: 0.25
      }).chain(this.reset.bind(this));
    } else {
      this.reset();
    }
  },

  reset: function(){
    this.idle = true;
    this.clone.destroy();
    this.fireEvent('complete', this.element);
  },

  serialize: function(){
    var params = Array.link(arguments, {modifier: Function.type, index: $defined});
    var serial = this.lists.map(function(list){
      return list.getChildren().map(params.modifier || function(element){
        return element.get('id');
      }, this);
    }, this);
    
    var index = params.index;
    if (this.lists.length == 1) index = 0;
    return $chk(index) && index >= 0 && index < this.lists.length ? serial[index] : serial;
  }

});

/*
Script: Tips.js
  Class for creating nice tips that follow the mouse cursor when hovering an element.

License:
  MIT-style license.
*/

var Tips = new Class({

  Implements: [Events, Options],

  options: {
    onShow: function(tip){
      tip.setStyle('visibility', 'visible');
    },
    onHide: function(tip){
      tip.setStyle('visibility', 'hidden');
    },
    showDelay: 100,
    hideDelay: 100,
    className: null,
    offsets: {x: 16, y: 16},
    fixed: false
  },

  initialize: function(){
    var params = Array.link(arguments, {options: Object.type, elements: $defined});
    this.setOptions(params.options || null);
    
    this.tip = new Element('div').inject(document.body);
    
    if (this.options.className) this.tip.addClass(this.options.className);
    
    var top = new Element('div', {'class': 'tip-top'}).inject(this.tip);
    this.container = new Element('div', {'class': 'tip'}).inject(this.tip);
    var bottom = new Element('div', {'class': 'tip-bottom'}).inject(this.tip);

    this.tip.setStyles({position: 'absolute', top: 0, left: 0, visibility: 'hidden'});
    
    if (params.elements) this.attach(params.elements);
  },
  
  attach: function(elements){
    $$(elements).each(function(element){
      var title = element.retrieve('tip:title', element.get('title'));
      var text = element.retrieve('tip:text', element.get('rel') || element.get('href'));
      var enter = element.retrieve('tip:enter', this.elementEnter.bindWithEvent(this, element));
      var leave = element.retrieve('tip:leave', this.elementLeave.bindWithEvent(this, element));
      element.addEvents({mouseenter: enter, mouseleave: leave});
      if (!this.options.fixed){
        var move = element.retrieve('tip:move', this.elementMove.bindWithEvent(this, element));
        element.addEvent('mousemove', move);
      }
      element.store('tip:native', element.get('title'));
      element.erase('title');
    }, this);
    return this;
  },
  
  detach: function(elements){
    $$(elements).each(function(element){
      element.removeEvent('mouseenter', element.retrieve('tip:enter') || $empty);
      element.removeEvent('mouseleave', element.retrieve('tip:leave') || $empty);
      element.removeEvent('mousemove', element.retrieve('tip:move') || $empty);
      element.eliminate('tip:enter').eliminate('tip:leave').eliminate('tip:move');
      var original = element.retrieve('tip:native');
      if (original) element.set('title', original);
    });
    return this;
  },
  
  elementEnter: function(event, element){
    
    $A(this.container.childNodes).each(Element.dispose);
    
    var title = element.retrieve('tip:title');
    
    if (title){
      this.titleElement = new Element('div', {'class': 'tip-title'}).inject(this.container);
      this.fill(this.titleElement, title);
    }
    
    var text = element.retrieve('tip:text');
    if (text){
      this.textElement = new Element('div', {'class': 'tip-text'}).inject(this.container);
      this.fill(this.textElement, text);
    }
    
    this.timer = $clear(this.timer);
    this.timer = this.show.delay(this.options.showDelay, this);

    this.position((!this.options.fixed) ? event : {page: element.getPosition()});
  },
  
  elementLeave: function(event){
    $clear(this.timer);
    this.timer = this.hide.delay(this.options.hideDelay, this);
  },
  
  elementMove: function(event){
    this.position(event);
  },
  
  position: function(event){
    var size = window.getSize(), scroll = window.getScroll();
    var tip = {x: this.tip.offsetWidth, y: this.tip.offsetHeight};
    var props = {x: 'left', y: 'top'};
    for (var z in props){
      var pos = event.page[z] + this.options.offsets[z];
      if ((pos + tip[z] - scroll[z]) > size[z]) pos = event.page[z] - this.options.offsets[z] - tip[z];
      this.tip.setStyle(props[z], pos);
    }
  },
  
  fill: function(element, contents){
    (typeof contents == 'string') ? element.set('html', contents) : element.adopt(contents);
  },

  show: function(){
    this.fireEvent('show', this.tip);
  },

  hide: function(){
    this.fireEvent('hide', this.tip);
  }

});

/*
Script: SmoothScroll.js
  Class for creating a smooth scrolling effect to all internal links on the page.

License:
  MIT-style license.
*/

var SmoothScroll = new Class({

  Extends: Fx.Scroll,

  initialize: function(options, context){
    context = context || document;
    var doc = context.getDocument(), win = context.getWindow();
    this.parent(doc, options);
    this.links = (this.options.links) ? $$(this.options.links) : $$(doc.links);
    var location = win.location.href.match(/^[^#]*/)[0] + '#';
    this.links.each(function(link){
      if (link.href.indexOf(location) != 0) return;
      var anchor = link.href.substr(location.length);
      if (anchor && $(anchor)) this.useLink(link, anchor);
    }, this);
    if (!Browser.Engine.webkit419) this.addEvent('complete', function(){
      win.location.hash = this.anchor;
    }, true);
  },

  useLink: function(link, anchor){
    link.addEvent('click', function(event){
      this.anchor = anchor;
      this.toElement(anchor);
      event.stop();
    }.bind(this));
  }

});

/*
Script: Slider.js
  Class for creating horizontal and vertical slider controls.

License:
  MIT-style license.
*/

var Slider = new Class({

  Implements: [Events, Options],

  options: {/*
    onChange: $empty,
    onComplete: $empty,*/
    onTick: function(position){
      if(this.options.snap) position = this.toPosition(this.step);
      this.knob.setStyle(this.property, position);
    },
    snap: false,
    offset: 0,
    range: false,
    wheel: false,
    steps: 100,
    mode: 'horizontal'
  },

  initialize: function(element, knob, options){
    this.setOptions(options);
    this.element = $(element);
    this.knob = $(knob);
    this.previousChange = this.previousEnd = this.step = -1;
    this.element.addEvent('mousedown', this.clickedElement.bind(this));
    if (this.options.wheel) this.element.addEvent('mousewheel', this.scrolledElement.bindWithEvent(this));
    var offset, limit = {}, modifiers = {'x': false, 'y': false};
    switch (this.options.mode){
      case 'vertical':
        this.axis = 'y';
        this.property = 'top';
        offset = 'offsetHeight';
        break;
      case 'horizontal':
        this.axis = 'x';
        this.property = 'left';
        offset = 'offsetWidth';
    }
    this.half = this.knob[offset] / 2;
    this.full = this.element[offset] - this.knob[offset] + (this.options.offset * 2);
    this.min = $chk(this.options.range[0]) ? this.options.range[0] : 0;
    this.max = $chk(this.options.range[1]) ? this.options.range[1] : this.options.steps;
    this.range = this.max - this.min;
    this.steps = this.options.steps || this.full;
    this.stepSize = Math.abs(this.range) / this.steps;
    this.stepWidth = this.stepSize * this.full / Math.abs(this.range) ;
    
    this.knob.setStyle('position', 'relative').setStyle(this.property, - this.options.offset);
    modifiers[this.axis] = this.property;
    limit[this.axis] = [- this.options.offset, this.full - this.options.offset];
    this.drag = new Drag(this.knob, {
      snap: 0,
      limit: limit,
      modifiers: modifiers,
      onDrag: this.draggedKnob.bind(this),
      onStart: this.draggedKnob.bind(this),
      onComplete: function(){
        this.draggedKnob();
        this.end();
      }.bind(this)
    });
    if (this.options.snap) {
      this.drag.options.grid = Math.ceil(this.stepWidth);
      this.drag.options.limit[this.axis][1] = this.full;
    }
  },

  set: function(step){
    if (!((this.range > 0) ^ (step < this.min))) step = this.min;
    if (!((this.range > 0) ^ (step > this.max))) step = this.max;
    
    this.step = Math.round(step);
    this.checkStep();
    this.end();
    this.fireEvent('tick', this.toPosition(this.step));
    return this;
  },

  clickedElement: function(event){
    var dir = this.range < 0 ? -1 : 1;
    var position = event.page[this.axis] - this.element.getPosition()[this.axis] - this.half;
    position = position.limit(-this.options.offset, this.full -this.options.offset);
    
    this.step = Math.round(this.min + dir * this.toStep(position));
    this.checkStep();
    this.end();
    this.fireEvent('tick', position);
  },
  
  scrolledElement: function(event){
    var mode = (this.options.mode == 'horizontal') ? (event.wheel < 0) : (event.wheel > 0);
    this.set(mode ? this.step - this.stepSize : this.step + this.stepSize);
    event.stop();
  },

  draggedKnob: function(){
    var dir = this.range < 0 ? -1 : 1;
    var position = this.drag.value.now[this.axis];
    position = position.limit(-this.options.offset, this.full -this.options.offset);
    this.step = Math.round(this.min + dir * this.toStep(position));
    this.checkStep();
  },

  checkStep: function(){
    if (this.previousChange != this.step){
      this.previousChange = this.step;
      this.fireEvent('change', this.step);
    }
  },

  end: function(){
    if (this.previousEnd !== this.step){
      this.previousEnd = this.step;
      this.fireEvent('complete', this.step + '');
    }
  },

  toStep: function(position){
    var step = (position + this.options.offset) * this.stepSize / this.full * this.steps;
    return this.options.steps ? Math.round(step -= step % this.stepSize) : step;
  },

  toPosition: function(step){
    return (this.full * Math.abs(this.min - step)) / (this.steps * this.stepSize) - this.options.offset;
  }

});

/*
Script: Scroller.js
  Class which scrolls the contents of any Element (including the window) when the mouse reaches the Element's boundaries.

License:
  MIT-style license.
*/

var Scroller = new Class({

  Implements: [Events, Options],

  options: {
    area: 20,
    velocity: 1,
    onChange: function(x, y){
      this.element.scrollTo(x, y);
    }
  },

  initialize: function(element, options){
    this.setOptions(options);
    this.element = $(element);
    this.listener = ($type(this.element) != 'element') ? $(this.element.getDocument().body) : this.element;
    this.timer = null;
    this.coord = this.getCoords.bind(this);
  },

  start: function(){
    this.listener.addEvent('mousemove', this.coord);
  },

  stop: function(){
    this.listener.removeEvent('mousemove', this.coord);
    this.timer = $clear(this.timer);
  },

  getCoords: function(event){
    this.page = (this.listener.get('tag') == 'body') ? event.client : event.page;
    if (!this.timer) this.timer = this.scroll.periodical(50, this);
  },

  scroll: function(){
    var size = this.element.getSize(), scroll = this.element.getScroll(), pos = this.element.getPosition(), change = {'x': 0, 'y': 0};
    for (var z in this.page){
      if (this.page[z] < (this.options.area + pos[z]) && scroll[z] != 0)
        change[z] = (this.page[z] - this.options.area - pos[z]) * this.options.velocity;
      else if (this.page[z] + this.options.area > (size[z] + pos[z]) && size[z] + size[z] != scroll[z])
        change[z] = (this.page[z] - size[z] + this.options.area - pos[z]) * this.options.velocity;
    }
    if (change.y || change.x) this.fireEvent('change', [scroll.x + change.x, scroll.y + change.y]);
  }

});

/*
Script: Accordion.js
  An Fx.Elements extension which allows you to easily create accordion type controls.

License:
  MIT-style license.
*/

var Accordion = new Class({

  Extends: Fx.Elements,

  options: {/*
    onActive: $empty,
    onBackground: $empty,*/
    display: 0,
    show: false,
    height: true,
    width: false,
    opacity: true,
    fixedHeight: false,
    fixedWidth: false,
    wait: false,
    alwaysHide: false
  },

  initialize: function(){
    var params = Array.link(arguments, {'container': Element.type, 'options': Object.type, 'togglers': $defined, 'elements': $defined});
    this.parent(params.elements, params.options);
    this.togglers = $$(params.togglers);
    this.container = $(params.container);
    this.previous = -1;
    if (this.options.alwaysHide) this.options.wait = true;
    if ($chk(this.options.show)){
      this.options.display = false;
      this.previous = this.options.show;
    }
    if (this.options.start){
      this.options.display = false;
      this.options.show = false;
    }
    this.effects = {};
    if (this.options.opacity) this.effects.opacity = 'fullOpacity';
    if (this.options.width) this.effects.width = this.options.fixedWidth ? 'fullWidth' : 'offsetWidth';
    if (this.options.height) this.effects.height = this.options.fixedHeight ? 'fullHeight' : 'scrollHeight';
    for (var i = 0, l = this.togglers.length; i < l; i++) this.addSection(this.togglers[i], this.elements[i]);
    this.elements.each(function(el, i){
      if (this.options.show === i){
        this.fireEvent('active', [this.togglers[i], el]);
      } else {
        for (var fx in this.effects) el.setStyle(fx, 0);
      }
    }, this);
    if ($chk(this.options.display)) this.display(this.options.display);
  },

  addSection: function(toggler, element, pos){
    toggler = $(toggler);
    element = $(element);
    var test = this.togglers.contains(toggler);
    var len = this.togglers.length;
    this.togglers.include(toggler);
    this.elements.include(element);
    if (len && (!test || pos)){
      pos = $pick(pos, len - 1);
      toggler.inject(this.togglers[pos], 'before');
      element.inject(toggler, 'after');
    } else if (this.container && !test){
      toggler.inject(this.container);
      element.inject(this.container);
    }
    var idx = this.togglers.indexOf(toggler);
    toggler.addEvent('click', this.display.bind(this, idx));
    if (this.options.height) element.setStyles({'padding-top': 0, 'border-top': 'none', 'padding-bottom': 0, 'border-bottom': 'none'});
    if (this.options.width) element.setStyles({'padding-left': 0, 'border-left': 'none', 'padding-right': 0, 'border-right': 'none'});
    element.fullOpacity = 1;
    if (this.options.fixedWidth) element.fullWidth = this.options.fixedWidth;
    if (this.options.fixedHeight) element.fullHeight = this.options.fixedHeight;
    element.setStyle('overflow', 'hidden');
    if (!test){
      for (var fx in this.effects) element.setStyle(fx, 0);
    }
    return this;
  },

  display: function(index){
    index = ($type(index) == 'element') ? this.elements.indexOf(index) : index;
    if ((this.timer && this.options.wait) || (index === this.previous && !this.options.alwaysHide)) return this;
    this.previous = index;
    var obj = {};
    this.elements.each(function(el, i){
      obj[i] = {};
      var hide = (i != index) || (this.options.alwaysHide && (el.offsetHeight > 0));
      this.fireEvent(hide ? 'background' : 'active', [this.togglers[i], el]);
      for (var fx in this.effects) obj[i][fx] = hide ? 0 : el[this.effects[fx]];
    }, this);
    return this.start(obj);
  }

});


/*
---

script: Mask.js

name: Mask

description: Creates a mask element to cover another.

license: MIT-style license

authors:
  - Aaron Newton

requires:
  - Core/Options
  - Core/Events
  - Core/Element.Event
  - /Class.Binds
  - /Element.Position
  - /IframeShim

provides: [Mask]

...
*/

var Mask = new Class({

  Implements: [Options, Events],

  Binds: ['position'],

  options: {/*
    onShow: function(){},
    onHide: function(){},
    onDestroy: function(){},
    onClick: function(){},
    inject: {
      where: 'after',
      target: null,
    },
    hideOnClick: false,
    id: null,
    destroyOnHide: false,*/
    style: {},
    'class': 'mask',
    maskMargins: false,
    useIframeShim: true,
    iframeShimOptions: {}
  },

  initialize: function(target, options){
    this.target = document.id(target) || document.id(document.body);
    this.target.store('mask', this);
    this.setOptions(options);
    this.render();
    this.inject();
  },

  render: function(){
    this.element = new Element('div', {
      'class': this.options['class'],
      id: this.options.id || 'mask-' + String.uniqueID(),
      styles: Object.merge(this.options.style, {
        display: 'none'
      }),
      events: {
        click: function(){
          this.fireEvent('click');
          if (this.options.hideOnClick) this.hide();
        }.bind(this)
      }
    });

    this.hidden = true;
  },

  toElement: function(){
    return this.element;
  },

  inject: function(target, where){
    where = where || (this.options.inject ? this.options.inject.where : '') || this.target == document.body ? 'inside' : 'after';
    target = target || (this.options.inject ? this.options.inject.target : '') || this.target;

    this.element.inject(target, where);

    if (this.options.useIframeShim){
      this.shim = new IframeShim(this.element, this.options.iframeShimOptions);

      this.addEvents({
        show: this.shim.show.bind(this.shim),
        hide: this.shim.hide.bind(this.shim),
        destroy: this.shim.destroy.bind(this.shim)
      });
    }
  },

  position: function(){
    this.resize(this.options.width, this.options.height);

    this.element.position({
      relativeTo: this.target,
      position: 'topLeft',
      ignoreMargins: !this.options.maskMargins,
      ignoreScroll: this.target == document.body
    });

    return this;
  },

  resize: function(x, y){
    var opt = {
      styles: ['padding', 'border']
    };
    if (this.options.maskMargins) opt.styles.push('margin');

    var dim = this.target.getComputedSize(opt);
    if (this.target == document.body){
      var win = window.getScrollSize();
      if (dim.totalHeight < win.y) dim.totalHeight = win.y;
      if (dim.totalWidth < win.x) dim.totalWidth = win.x;
    }
    this.element.setStyles({
      width: Array.pick([x, dim.totalWidth, dim.x]),
      height: Array.pick([y, dim.totalHeight, dim.y])
    });

    return this;
  },

  show: function(){
    if (!this.hidden) return this;

    window.addEvent('resize', this.position);
    this.position();
    this.showMask.apply(this, arguments);

    return this;
  },

  showMask: function(){
    this.element.setStyle('display', 'block');
    this.hidden = false;
    this.fireEvent('show');
  },

  hide: function(){
    if (this.hidden) return this;

    window.removeEvent('resize', this.position);
    this.hideMask.apply(this, arguments);
    if (this.options.destroyOnHide) return this.destroy();

    return this;
  },

  hideMask: function(){
    this.element.setStyle('display', 'none');
    this.hidden = true;
    this.fireEvent('hide');
  },

  toggle: function(){
    this[this.hidden ? 'show' : 'hide']();
  },

  destroy: function(){
    this.hide();
    this.element.destroy();
    this.fireEvent('destroy');
    this.target.eliminate('mask');
  }

});

Element.Properties.mask = {

  set: function(options){
    var mask = this.retrieve('mask');
    if (mask) mask.destroy();
    return this.eliminate('mask').store('mask:options', options);
  },

  get: function(){
    var mask = this.retrieve('mask');
    if (!mask){
      mask = new Mask(this, this.retrieve('mask:options'));
      this.store('mask', mask);
    }
    return mask;
  }

};

Element.implement({

  mask: function(options){
    if (options) this.set('mask', options);
    this.get('mask').show();
    return this;
  },

  unmask: function(){
    this.get('mask').hide();
    return this;
  }

});
