<div class="box-content">
	<p class="heading">Loại dữ liệu mẫu</p>
	<div class="form-group">
		<label class="form-check radio d-block">
			<input type="radio" name="rating_star_setting[autoDataType]" value="auto" class="js_rating_star_input_type form-check-input" {{ ($config['autoDataType'] == 'auto') ? 'checked' : ''}}> Dữ liệu có sẵn của sikido
		</label>
		<label class="form-check radio d-block">
			<input type="radio" name="rating_star_setting[autoDataType]" value="handmade" class="js_rating_star_input_type form-check-input" {{ ($config['autoDataType'] == 'handmade') ? 'checked' : ''}}> Tự tạo
		</label>
	</div>
	<div class="rating_star_data_box rating_star_data_handmade" id="js_rating_star_handmade_box" style="display:{{ ($config['autoDataType'] == 'auto') ? 'none' : 'block'}}" data-handmade="{{ htmlentities($dataAuto)}}">
		<div class="d-flex justify-content-between align-items-center mb-1">
			<p class="heading mb-0">Dữ liệu mẫu được áp dụng</p>
			<div>
				<button class="btn btn-blue" type="button" id="js_rating_star_handmade_btn_add">
					{!! Admin::icon('add')!!} Thêm dữ liệu
				</button>
			</div>
		</div>
		<div class="rating_star_handmade_table">
			<table class="display table table-striped media-table ">
				<thead>
				<tr>
					<th class="manage-column">Tên người đánh giá</th>
					<th class="manage-column">Nội dung đánh giá</th>
					<th class="manage-column">Hành động</th>
				</tr>
				</thead>
				<tbody id="js_rating_star_handmade_result"></tbody>
			</table>
		</div>
	</div>
</div>

<script id="rating_star_handmade_template" type="text/x-custom-template">
	<tr class="js_column js_rating_star_handmade_item tr_${id}">
		<td class="column">
			<input name="handmade[${id}][name]" value="${name}" class="form-control" />
		</td>
		<td class="column">
			<input name="handmade[${id}][message]" value="${message}" class="form-control" />
		</td>
		<td class="action column">
			<button class="btn btn-red js_rating_star_handmade_btn_delete" data-id="${id}">{!! Admin::icon('delete')!!}</button>
		</td>
	</tr>
</script>

<script>
	$(function () {
		class RatingStarAutoDataHandle {
			constructor() {
				this.type = $('.js_rating_star_input_type:checked').val();
				this.handmadeList = {
					items : [],
					get(id) {
						let objIndex = this.items.findIndex((item => item.id == id));
						if(objIndex == -1) return null;
						return this.items[objIndex]
					},
					add(obj) {
						let objIndex = this.items.findIndex((item => item.id == obj.id));
						if(objIndex == -1) {
							this.items.unshift(obj);
						}
						return this.items;
					},
					update(obj) {
						let objIndex = this.items.findIndex((item => item.id == obj.id));
						this.items[objIndex] = {...this.items[objIndex], ...obj};
						return this.items;
					},
					delete(id) {
						this.items = this.items.filter(function(item) {
							return item.id != id
						})
					},
				};
				this.handmadeTable = $('#js_rating_star_handmade_result');
				this.loadData()
			}
			loadData() {
				let self = this;
				let handmadeItems = $('#js_rating_star_handmade_box').data('handmade');
				if(handmadeItems != null && handmadeItems != '') {
					for (const [key, item] of Object.entries(handmadeItems)) {
						console.log(item)
						let handmade = {
							id: item.id,
							name: item.name,
							message: item.message,
						};
						self.handmadeList.add(handmade);
					}
					this.renderHandmade();
				}
			}
			changeType(element) {
				let typeNew = $('input.js_rating_star_input_type:checked').val();
				if(typeNew === 'handmade') {
					$('.rating_star_data_handmade').show();
				}
				if(typeNew === 'auto') {
					$('.rating_star_data_handmade').hide();
				}
				this.type = typeNew;
			}
			clickAddHandmade(element) {
				let item= { id: SkilldoUtil.uniqId(), name: '', message: ''};
				this.handmadeList.add(item);
				this.renderHandmade();
				return false;
			}
			deleteHandmade(element) {

				let id = element.attr('data-id');

				this.handmadeList.delete(id);

				element.closest('.js_rating_star_handmade_item').remove();

				return false;
			}
			renderHandmade(element) {

				let self = this;

				this.handmadeTable.html('');

				for (const [key, items_tmp] of Object.entries(this.handmadeList.items)) {
					let items = [items_tmp];
					self.handmadeTable.append(items.map(function(item) {
						return $('#rating_star_handmade_template').html().split(/\$\{(.+?)\}/g).map(render(item)).join('');
					}));
				}

				return false;
			}
			uploadHandmade(element) {
				let self = this;
				let data = $(':input', this.handmadeTable).serializeJSON();
				for (const [id, item] of Object.entries(data.handmade)) {
					let itemOld = self.handmadeList.get(id);
					if(itemOld !== null) {
						itemOld.name = item.name;
						itemOld.message = item.message;
						self.handmadeList.update(itemOld);
					}
				}
				return false;
			}
		}

		const ratingStarAutoData = new RatingStarAutoDataHandle();
		$(document)
				.on('click', '#js_rating_star_handmade_btn_add', function() {ratingStarAutoData.clickAddHandmade($(this))})
				.on('change', '#js_rating_star_handmade_result input', function() {ratingStarAutoData.uploadHandmade($(this))})
				.on('click', '.js_rating_star_handmade_btn_delete', function() {ratingStarAutoData.deleteHandmade($(this))})
				.on('change', '.js_rating_star_input_type', function() {ratingStarAutoData.changeType($(this))})
	})
</script>