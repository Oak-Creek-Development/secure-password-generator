jQuery(function($){

	const $boxes = $('.exclude');
	const $shortcode = $('#shortcode')[0];
	const shortcodeArgs = { exclude: '' };

	const copyAlert = $e => {
		$e.show();

		setTimeout(() => {
			$e.fadeOut(500, 'linear');
		}, 1600);
	}

	// returns a string that contains all the characters that are included in both input strings
	const strIntersect = (a, b) => {
		let s = new Set(b.split(''));
		return a.split('').filter(item => s.has(item)).join('');
	};

	const generateShortcode = () => {
		shortcodeArgs.exclude = $boxes.filter('.individual:checked').map(function(){
			return this.value;
		}).get().join('');

		let str = '';
		for(let i in shortcodeArgs){
			if(shortcodeArgs[i].length){
				str += shortcodeArgs[i];
			}
		}

		// use open and close tags to prevent problems when closing bracket "]" is part of the string
		str = '[secure_pw_gen]'+str.trim()+'[/secure_pw_gen]';
		
		$shortcode.size = str.length;
		$shortcode.value = str;
	}

	$(document).ready(function(){

		generateShortcode();

		$boxes.on('change', function(){

			// if a group box was clicked, check all corresponding individual boxes
			if(this.classList.contains('group')){
				let groupVals = this.value;
				$boxes.filter(function(){
					return this.classList.contains('individual') && groupVals.includes(this.value);
				}).prop('checked', this.checked);
			}

			generateShortcode();

			// if an individual box was clicked, set state of group boxes appropriately
			if(this.classList.contains('individual')){
				$boxes.filter('.group').each(function(){
					let common = strIntersect(this.value, shortcodeArgs.exclude);
					$(this).prop('checked', this.value.length === common.length);
				});
			}

		});

		$('#copy').on('click', function(e){
			this.blur();
			navigator.clipboard.writeText($shortcode.value).then(() => {
				copyAlert($('#copy-success'));
			}, () => {
				copyAlert($('#copy-fail'));
			});
		});

	});

});
