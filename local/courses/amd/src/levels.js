/**
 * Add a create new group modal to the page.
 *
 * @module     local_courses/levels
 * @class      levels
 * @package    local_courses
 * @copyright  eabyas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['local_courses/jquery.dataTables', 'jquery', 'core/str', 'core/modal_factory', 'core/modal_events',
  'core/fragment', 'core/ajax', 'core/yui', 'jqueryui'],
  function (DataTable, $, Str, ModalFactory, ModalEvents, Fragment, Ajax, Y) {

    /**
    * Constructor
    *
    * @param {object} args
    *
    * Each call to init gets it's own instance of this class.
    */
    var levels = function (args) {
        this.contextid = args.contextid;
        this.args = args;
        this.init(args);
    };

    /**
    * @var {Modal} modal
    * @private
    */
    levels.prototype.modal = null;

    /**
    * @var {int} contextid
    * @private
    */
    levels.prototype.contextid = -1;

    /**
    * Initialise the class.
    *
    * @param {String} selector used to find triggers for the new group modal.
    * @private
    * @return {Promise}
    */
    levels.prototype.init = function (args) {

      var self = this;
      if (args.id) {
        var head = Str.get_string('update_level', 'local_courses');
      } else {
        var head = Str.get_string('add_levels', 'local_courses');
      }
      return head.then(function (title) {
        //return Str.get_string('add_coursetype', 'local_courses', self).then(function (title) {
        // Create the modal.
        return ModalFactory.create({
          type: ModalFactory.types.SAVE_CANCEL,
          title: title,
          body: self.getBody()
        });
      }.bind(self)).then(function (modal) {

        // Keep a reference to the modal.
        self.modal = modal;
        // self.modal.show();
        // Forms are big, we want a big modal.
        // self.modal.setLarge();
        this.modal.getRoot().addClass('openLMStransition');

        // We want to reset the form every time it is opened.
        this.modal.getRoot().on(ModalEvents.hidden, function () {
          this.modal.getRoot().animate({ "right": "-85%" }, 500);
          setTimeout(function () {
            modal.destroy();
          }, 1000);
        }.bind(this));

        // We want to hide the submit buttons every time it is opened.
        self.modal.getRoot().on(ModalEvents.shown, function () {
          self.modal.getRoot().append('<style>[data-fieldtype=submit] { display: none ! important; }</style>');
          this.modal.getFooter().find('[data-action="cancel"]').on('click', function () {
            modal.hide();
            setTimeout(function () {
              modal.destroy();
            }, 1000);
            // modal.destroy();
          });
        }.bind(this));


        // We catch the modal save event, and use it to submit the form inside the modal.
        // Triggering a form submission will give JS validation scripts a chance to check for errors.
        self.modal.getRoot().on(ModalEvents.save, self.submitForm.bind(self));
        // We also catch the form submit event and use it to submit the form with ajax.
        self.modal.getRoot().on('submit', 'form', self.submitFormAjax.bind(self));
        self.modal.show();
        this.modal.getRoot().animate({ "right": "0%" }, 500);
        return this.modal;
      }.bind(this));
    };



    /**
    * @method getBody
    * @private
    * @return {Promise}
    */
    levels.prototype.getBody = function (formdata) {
      if (typeof formdata === "undefined") {
        formdata = {};
      }
      this.args.jsonformdata = JSON.stringify(formdata);
      return Fragment.loadFragment('local_courses', 'level_form', this.contextid, this.args);

    };
    /**
    * @method handleFormSubmissionResponse
    * @private
    * @return {Promise}
    */
    levels.prototype.handleFormSubmissionResponse = function () {
      this.modal.hide();
      // We could trigger an event instead.
      // Yuk.
      Y.use('moodle-core-formchangechecker', function () {
        M.core_formchangechecker.reset_form_dirty_state();
      });
      document.location.reload();
    };

    /**
     * @method handleFormSubmissionFailure
     * @private
     * @return {Promise}
     */
    levels.prototype.handleFormSubmissionFailure = function (data) {
      // Oh noes! Epic fail :(
      // Ah wait - this is normal. We need to re-display the form with errors!
      this.modal.setBody(this.getBody(data));
    };

    /**
     * Private method
     *
     * @method submitFormAjax
     * @private
     * @param {Event} e Form submission event.
     */
    levels.prototype.submitFormAjax = function (e, args) {
      // We don't want to do a real form submission.
      e.preventDefault();
      // Convert all the form elements values to a serialised string.
      var formData = this.modal.getRoot().find('form').serialize();
      var params = {};
      params.contextid = this.contextid;
      params.jsonformdata = JSON.stringify(formData);
      // alert(this.contextid);
      // Now we can continue...
      var promis = Ajax.call([{
        methodname: 'local_courses_submit_level_form',
        args: params,
        done: this.handleFormSubmissionResponse.bind(this, formData),
        fail: this.handleFormSubmissionFailure.bind(this, formData)
      }]);

    };


    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    levels.prototype.submitForm = function (e) {
      e.preventDefault();
      var self = this;
      self.modal.getRoot().find('form').submit();
    };

    return /** @alias module:local_courses/levels */ {
      // Public variables and functions.
      /**
      * @param {string} args
       * @return {Promise}
       */
      init: function (args) {

        return new levels(args);
      },
      Datatable: function (args) {
        Str.get_strings([{
          key: 'search',
          //component: 'local_costcenter',
        }]).then(function (str) {
          $('#manage_courseproviders').dataTable({
            "searching": true,
            "responsive": true,
            "aaSorting": [[ 1, "desc" ]],
            "lengthMenu": [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
            "aoColumnDefs": [{ 'bSortable': false, 'aTargets': [1] }],
            language: {
              search: "_INPUT_",
              searchPlaceholder: str[0],
              "paginate": {
                "next": ">",
                "previous": "<"
              }
            }
          });
        }.bind(this));
      },


      deleteConfirm: function (args) {
        return Str.get_strings([{
          key: 'confirm'
        },
        {
          key: 'deletelevel',
          component: 'local_courses',
          param: args
        }]).then(function (s) {
          ModalFactory.create({
            title: args.title,
            type: ModalFactory.types.DEFAULT,
            body: s[1],
            footer: '<button type="button" class="btn btn-primary" data-action="save">Yes</button>&nbsp;' +
              '<button type="button" class="btn btn-secondary" data-action="cancel">No</button>'
          }).done(function (modal) {
            this.modal = modal;
            modal.getRoot().find('[data-action="save"]').on('click', function () {
              args.confirm = true;
              var params = {};
              params.id = args.id;
              var promise = Ajax.call([{
                methodname: 'local_courses_delete_level',
                args: params

              }]);
              promise[0].done(function () {
                window.location.href = window.location.href;
              }).fail(function (ex) {
                // do something with the exception
                console.log(ex);
              });
            }.bind(this));
            modal.getFooter().find('[data-action="cancel"]').on('click', function () {
              modal.setBody('');
              modal.hide();
            });
            modal.show();
          }.bind(this));
        }.bind(this));
      },

      
      statusConfirm: function(args) {
            args.confirm = true;
            var promise = Ajax.call([{
                methodname: 'local_courses_change_level_status',
                args: {
                    id: args.id,
                },
            }]);
            promise[0].done(function() {
               window.location.reload();
            });
        },

      load: function () {
      }
    };

  });