var app = new Vue({
	el: '#app',
	data: {
		login: '',
		pass: '',
		post: false,
		invalidLogin: false,
		invalidPass: false,
		invalidSum: false,
		serverAuthorizationError: false,
		posts: [],
		addSum: 0,
		amount: 0,
		likes: 0,
		commentText: '',
		parentForNewComment: null,
		packs: [
			{
				id: 1,
				price: 5
			},
			{
				id: 2,
				price: 20
			},
			{
				id: 3,
				price: 50
			},
		],
	},
	computed: {
		test: function () {
			var data = [];
			return data;
		}
	},
	created(){
		var self = this
		axios
			.get('index.php/main_page/get_all_posts')
			.then(function (response) {
				self.posts = response.data.posts;
			})
	},
	methods: {
		logout: function () {
			console.log ('logout');
		},
		logIn: function () {
			var self= this;
			if(self.login === ''){
				self.invalidLogin = true
			}
			else if(self.pass === ''){
				self.invalidLogin = false
				self.invalidPass = true
			}
			else{
				self.invalidLogin = false
				self.invalidPass = false
				axios.post('main_page/login', {
					login: self.login,
					password: self.pass
				})
					.then(function (response) {
						setTimeout(function () {
							$('#loginModal').modal('hide');
							location.reload()
						}, 500);
					})
					.catch(function (error) {
						if (error.response.status == 401) {
							self.serverAuthorizationError = error.response.data.error_message
						}
					});
			}
		},
		fiilIn: function () {
			var self= this;
			if(self.addSum === 0){
				self.invalidSum = true
			}
			else{
				self.invalidSum = false
				axios.post('/main_page/add_money', {
					sum: self.addSum,
				})
					.then(function (response) {
						setTimeout(function () {
							$('#addModal').modal('hide');
						}, 500);
					})
			}
		},
		openPost: function (id) {
			var self= this;
			axios
				.get('/main_page/get_post/' + id)
				.then(function (response) {
					self.post = response.data.post;
					if(self.post){
						setTimeout(function () {
							$('#postModal').modal('show');
						}, 500);
					}
				})
		},
		addLike: function (id) {
			var self= this;
			axios
				.get('/main_page/like?post_id='+self.post.id)
				.then(function (response) {
					self.post.likes = response.data.likes;
				})
		},
		addLikeForComment: function (idx) {
			var self= this;
			axios
				.get('/main_page/like?comment_id='+self.post.coments[idx].id)
				.then(function (response) {
					self.post.coments[idx].likes = response.data.likes;
				})
		},
		buyPack: function (id) {
			var self= this;
			axios.post('/main_page/buy_boosterpack', {
				id: id,
			})
				.then(function (response) {
					self.amount = response.data.amount
					if(self.amount !== 0){
						setTimeout(function () {
							$('#amountModal').modal('show');
						}, 500);
					}
				})
		},
		openAnswerForm: function (commentIdx) {
			this.parentForNewComment = commentIdx;
		},
		openCommentForm: function (comment) {
			this.parentForNewComment = 'none';
		},
		sendComment: function (comment) {
			if (!this.commentText) {
				return
			}

			let sendObj = {};
			if (this.post.coments[this.parentForNewComment] != undefined) {
				sendObj.path = this.post.coments[this.parentForNewComment].path;
			}
			sendObj.postId   = this.post.id
			sendObj.text     = this.commentText

			let self = this;
			axios.post('main_page/comment', sendObj)
				.then(function (response) {
					self.post = response.data.post;
					self.parentForNewComment = null;
					self.commentText = '';
				})
		},
	}
});


