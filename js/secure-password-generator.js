jQuery(function($){

	$(document).ready(function(){

		$('.ocdpw').each(function(){
			let $this = $(this);
			let config = window[$this.data('instance')];
			//console.log($this);
			//console.log(config.chars.special);

			/*for(const i in config.chars.special){
				console.log(config.chars.special[i]);
				//config.chars.special[i] = $("<textarea/>").html(config.chars.special[i]).text();
				if('<' == config.chars.special[i]){
					config.chars.special[i] = '&lt;';
				}
				if('>' == config.chars.special[i]){
					config.chars.special[i] = '&gt;';
				}
				if('&' == config.chars.special[i]){
					config.chars.special[i] = '&amp;';
				}
			}*/

			/*console.log(config);*/
			//console.log(config.chars.special);

			const charsR = config.chars.number.concat(config.entities.special, config.chars.lower, config.chars.upper);
			//const charsR = config.chars.special;

			//console.log(charsR);

			$divRandom = $this.find('.ocdpw-random');
			$divFeedback = $this.find('.ocdpw-feedback');

			let rand = '';
			let prev = '';

			// build random char string
			for(let i = 0; i < config.atts.height * 2; i++){

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

			$divRandom.append(rand);




			const $good = $('<span class="ocdpw-good">'+config.msg.good+'</span>');
			const $bad = $('<span class="ocdpw-bad">'+config.msg.bad+'</span>');

			$divFeedback.append('<p class="ocdpw-count">'+config.msg.count+'</p>');

			for(const i in config.chars){
				$divFeedback.append('<p class="ocdpw-'+i+'">'+config.msg[i]+'</p>');
			}

			$divFeedback.children('p').append($bad.clone());

			const $count = $divFeedback.children('p.ocdpw-count').children('span');
			$count.text('0');








			$this.show();
			//$this.find('textarea').autoResize().trigger('change.dynSiz');



			







			document.addEventListener('selectionchange', () => {

				let selection = window.getSelection();
				let isCurrentInstance = false;

				if(0 < selection.rangeCount){
					let range = selection.getRangeAt(0);

					let commonAncestor = range.commonAncestorContainer;
					if(commonAncestor.nodeType === Node.TEXT_NODE){
						commonAncestor = commonAncestor.parentNode.parentNode;
					}

					if($(commonAncestor).prop('id') == 'ocdpw-random-'+$this.data('instance')){
						isCurrentInstance = true;
					}
				}

				let selR = Array.from(selection.toString());

				// character count requirement
				if(isCurrentInstance){
					$count.text(selR.length);
				}else{
					$count.text('0');
				}

				if(isCurrentInstance && 17 < selR.length && 33 > selR.length){
					$count.addClass('ocdpw-good');
				}else{
					$count.removeClass('ocdpw-good');
				}

				// boolean requirements
				for(const i in config.chars){
					// array intersection of selection and character type
					if(isCurrentInstance && config.chars[i].filter(x => selR.indexOf(x) !== -1).length){
						$this.find('.ocdpw-'+i+' > span').replaceWith($good.clone());
					}else{
						$this.find('.ocdpw-'+i+' > span').replaceWith($bad.clone());
					}
				}

			});


		});

	});

});
