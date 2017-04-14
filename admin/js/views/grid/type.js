
wpmoly = window.wpmoly || {};

var Grid = wpmoly.view.Grid || {};

_.extend( Grid, {

	Type: wp.Backbone.View.extend({

		template: wp.template( 'wpmoly-grid-builder-type-metabox' ),

		events: {
			'click [data-action="grid-type"]'  : 'setType',
			'click [data-action="grid-mode"]'  : 'setMode',
			'click [data-action="grid-theme"]' : 'setTheme'
		},

		/**
		 * Initialize the View.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		initialize: function( options ) {

			this.controller = options.controller || {};
			this.model = this.controller.builder;

			this.bindEvents();
		},

		/**
		 * Bind events.
		 *
		 * @since    3.0
		 *
		 * @return   void
		 */
		bindEvents: function() {

			this.listenTo( this.model, 'change:type',  this.render );
			this.listenTo( this.model, 'change:mode',  this.render );
			this.listenTo( this.model, 'change:theme', this.render );

			wpmoly.$( '[data-action="customize-grid"]' ).on( 'click', this.toggle );
		},

		/**
		 * Toggle the Metabox.
		 * 
		 * If a 'click' event is passed, trigger the default WP Metabox
		 * toggle process.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' event
		 * 
		 * @return   void
		 */
		toggle: function( e ) {

			if ( e ) {
				wpmoly.$( '#wpmoly-grid-type-metabox .handlediv' ).trigger( 'click' );
			}

			var closed = wpmoly.$( '#wpmoly-grid-type-metabox' ).hasClass( 'closed' );

			wpmoly.$( '#customize-grid' ).toggleClass( 'active', ! closed );
		},

		/**
		 * Set grid type.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event.
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		setType: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setType( value );

			return this;
		},

		/**
		 * Set grid mode.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event.
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		setMode: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setMode( value );

			return this;
		},

		/**
		 * Set grid theme.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event.
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		setTheme: function( event ) {

			var $elem = this.$( event.currentTarget ),
			    value = $elem.attr( 'data-value' );

			this.controller.setTheme( value );

			return this;
		},

		/**
		 * Render the View.
		 * 
		 * @since    3.0
		 * 
		 * @param    object    JS 'click' Event.
		 * 
		 * @return   Returns itself to allow chaining.
		 */
		render: function() {

			this.toggle();

			this.$el.html( this.template(
				_.extend( this.model.toJSON(), {
					types  : this.model.types,
					modes  : this.model.modes,
					themes : this.model.themes
				} )
			) );

			return this;
		}
	})

});
