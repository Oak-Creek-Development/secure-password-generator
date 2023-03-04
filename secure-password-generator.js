jQuery(function($){

	// do stuff on page load and each time tab regains focus
	function ocdpwInit(charsR){

		let rand = '';
		let prev = '';

		// build random char string
		for(let i = 0; i < 12; i++){
			rand += '<span>';

			for(let j = 0; j < 32; j++){

				let cur = charsR[Math.floor(Math.random() * charsR.length)];

				// prevent repetition
				if(cur == prev){
					j--;
				}else{
					prev = cur;
					rand += cur;
				}

			}

			rand += '</span>';
		}

		// deselect text
		window.getSelection().removeAllRanges();

		// remove random char string
		$('.ocdpw > span').remove();

		// insert new random char string
		$('.ocdpw').prepend(rand);

		//TODO: check if page has a script that prevents text copy and disable the nocopy for only this block (not sure if possible)

	}

	$(document).ready(function(){

		// keep only the first instance of the output div
		const $dupe = $('.ocdpw > .ocdpw-dupe').detach();
		$('.ocdpw').each(function(i){
			if(0 < i){
				$(this).replaceWith($dupe.clone()[0]);
			}
		});

		$('.ocdpw').show();

		const ocdpw = {
			number: [
				'2', '3', '4',      '6', '7', '8', '9',
				'2', '3', '4',      '6', '7', '8', '9',
				'2', '3', '4',      '6', '7', '8', '9'
			],
			special: [
				'&amp;', '&lt;', '&gt;', '~', '!', '@', '#', '$', '%', '^', '*', '(', ')', '_', '-', '+', '=', '{', '}', '[', ']', ':', ',', '.', '?'
			],
			lower: [
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',      'j', 'k',      'm', 'n',      'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',      'j', 'k',      'm', 'n',      'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',      'j', 'k',      'm', 'n',      'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
			],
			upper: [
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',      'J', 'K', 'L', 'M', 'N',      'P', 'Q', 'R',      'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',      'J', 'K', 'L', 'M', 'N',      'P', 'Q', 'R',      'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
			]
		};

		const charsR = ocdpw.number.concat(ocdpw.special, ocdpw.lower, ocdpw.upper);

		// a few chars that are represented as HTML entities above because they cause problems with the HTML
		// but they need to be in single character form later when matching with selected text on the page
		ocdpw.special[0] = '&';
		ocdpw.special[1] = '<';
		ocdpw.special[2] = '>';

		const $good = $('.ocdpw > .ocdpw-good').detach();
		const $bad = $('.ocdpw > .ocdpw-bad').detach();

		// add default state to requirement indictors
		for(const i in ocdpw){
			$bad.clone().appendTo('.ocdpw-' + i);
		}

		ocdpwInit(charsR);

		// regenerate each time the tab regains focus
		$(window).on('focus', function(){
			ocdpwInit(charsR);
		});

		// set width to 64 characters (32 characters on mobile)
		$('.ocdpw').css('max-width', $('.ocdpw > span').width() * 2);
		$('.ocdpw > p').css('max-width', $('.ocdpw > span').width());




		const $count = $('.ocdpw > .ocdpw-count > span');

		document.addEventListener('selectionchange', () => {

			let selR = Array.from(window.getSelection().toString());

			// character count requirement
			$count.text(selR.length);

			if(17 < selR.length && 33 > selR.length){
				$count.addClass('ocdpw-good');
			}else{
				$count.removeClass('ocdpw-good');
			}

			// boolean requirements
			for(const i in ocdpw){
				// array intersection of selection and character type
				if(ocdpw[i].filter(x => selR.indexOf(x) !== -1).length){
					$('.ocdpw > .ocdpw-'+i+' > span').replaceWith($good.clone());
				}else{
					$('.ocdpw > .ocdpw-'+i+' > span').replaceWith($bad.clone());
				}
			}

		});

	});

});
