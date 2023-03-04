jQuery(function($){

	$(document).ready(function(){

		$('.ocdpw').each(function(){
			let $this = $(this);
			let config = window[$this.data('instance')];
			//console.log($this);
			console.log(config.chars.special);

			for(const i in config.chars.special){
				config.chars.special[i] = $("<textarea/>").html(config.chars.special[i]).text();
			}

			/*console.log(config);*/
			//console.log(config.chars.special);

			const charsR = config.chars.number.concat(config.chars.special, config.chars.lower, config.chars.upper);
			//const charsR = config.chars.special;

			//console.log(charsR);

			$divRandom = $this.find('.ocdpw-random');
			$divFeedback = $this.find('.ocdpw-feedback');

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

			$divRandom.prepend(rand);

			$this.show();
		});

	});

});
