(function($, config){

	$(function(){
		console.log(config);

		// UI ITEMS
		const $root = $("#"+config.root_id);
		const $items = $("<div></div>")
			.addClass("additional-resource__items")
			.appendTo($root);

		const $add = $("<button></button>")
			.text(config.strings.add)
			.addClass("button additional-resources__add")
			.appendTo($root);

		// UI functions
		function nextIndex() {
			return $items.children().length;
		}

		function renderItem(pos) {
			const item = config.resources[pos];
			const vals = {
				url: "",
				type: "",
				position: "",
				inplace: "",
			};
			if(typeof item !== typeof undefined){
				vals.url = item.url;
				vals.type = item.type;
				vals.position = item.position;
				vals.inplace = item.inplace;
			}

			const $item = $("<div></div>").addClass("additional-resource__item");

			$("<label>"+config.strings.itemLabel+"<br/><input name='"+config.post_key+"[url][]' type='text' value='"+vals.url+"' /></label>").appendTo($item);


			$.each(config.options, function(index, options){
				renderSelect(
					config.post_key+"["+index+"][]",
					options,
					vals[index]
				).appendTo($item);
			});

			$("<a></a>")
				.text(config.strings.delete)
				.on("click",()=>{
					$item.remove();
				})
				.addClass("delete additional-resource__delete")
				.appendTo($item);

			return $item;
		}

		function renderSelect(name, options, val){
			const $select = $("<select name='"+name+"'></select>");
			for(let i = 0; i < options.length; i++){
				const selected = (options[i].value === val )? "selected":"";
				$("<option value='"+options[i].value+"' "+selected+">"+options[i].name+"</option>").appendTo($select);
			}
			return $select;
		}

		function addNewResource() {
			$items.append(renderItem(nextIndex()));
		}

		// INIT
		if(typeof config.resources === typeof []){
			for(let i = 0; i < config.resources.length; i++){
				$items.append(renderItem(i));
			}
		}
		if(nextIndex() < 1){
			$items.append(renderItem(nextIndex()));
		}

		$add.on("click", function(e){
			e.preventDefault();
			addNewResource();
		});

		// remove all items, state is now in HTML
		config.resources = [];

	});

})(jQuery, AdditionalResources);