(function ($, api) {
  'use strict';

  api.RShControl = api.Control.extend({
    ready: function () {
      var that = this;

      this.setting.bind(function () {
        that._updateCb && that._updateCb();
      });

      this.isReady = true;
    },

    update: function (cb) {
      if (!this.isReady) {
        return;
      }

      if (typeof cb == 'function') {
        this._updateCb = $.proxy(cb, this);
      }

      this._doUpdate();
    },

    _doUpdate: function () {
      this.setting(this.getData());
    },

    getData: function () {
      throw new Error('Not implemented');
    }
  });

  api.RShEditorControl = api.RShControl.extend({
    ready: function () {
      var editorId = this.container
        .find('.rsh-editor:first')
        .attr('id');

      var change = $.proxy(this.update, this);

      this.editor = new RShEditor({
        id: editorId,
        change: change,
      });

      this.constructor.__super__.ready.call(this);
    },

    getData: function () {
      return this.editor.getContent();
    },
  });

  api.RShLinkControl = api.RShControl.extend({
    ready: function () {
      this.$inputs = this.container
        .find('.rsh-input')
        .on('change keyup', $.proxy(this.update, this));

      this.constructor.__super__.ready.call(this);
    },

    getData: function () {
      var data = this.$inputs
        .map(function () {
          return $(this).val().trim();
        })
        .get();
        // Если сохранить массив с одним элементом будет ошибка
        // .filter(function (value) {
        //   return !!value;
        // });

      return JSON.stringify(data);
    },
  });

  // TODO: Сделать санацию
  api.RShPortletsControl = api.RShControl.extend({
    ready: function () {
      var that = this;

      this.$input = this.container
        .find('.rsh-input:first')
        .on('change keyup', function () {
          if (that.isReady) {
            that.$addBtn.prop(
              'disabled',
              !that.$input.val().trim()
            );
          }
        });

      this.$addBtn = this.container
        .find('.rsh-btn:first')
        .prop('disabled', true)
        .click(function () {
          if (that.isReady) {
            that.addPortlet({
              title: that.$input.val().trim()
            });
          }
        });

      this.$area = this.container
        .find('.rsh-portlets-area:first')
        .sortable({
          handle: '.rsh-portlet-header',
          scroll: false,

          stop: function (e, ui) {
            // HACK: Обновляем portlet т.к. tinymce конфликтует 
            // с sortable и при перетаскивании становится неактивным
            var portlet = ui.item.data('rsh.portlet');
            portlet && portlet.refresh();
          },

          update: function () {
            if (that.isReady) {
              that.update();
            }
          }
        });

      this._initPortlets();
      this.constructor.__super__.ready.call(this);
    },

    _initPortlets: function () {
      this.params.items.forEach(this.addPortlet, this);
    },

    addPortlet: function (data) {
      var newPortlet = this.createPortlet(data);
      this.$area.append(newPortlet.$element);
    },

    createPortlet: function (data) {
      var that = this;
      var timerId;

      var options = $.extend({}, data, {
        id: _.uniqueId('rsh-portlet-'),
        canUpload: that.params.canUpload,

        ready: function () {
          that.$input.val('').trigger('change');
          that.update();
        },

        change: function () {
          // HACK: Небольшая оптимизация вызовов обновления
          clearTimeout(timerId);
          timerId = setTimeout(function (_this) {
            that.update(function () {
              _this.trigger('changed');
            });
          }, 500, this);
        },

        remove: function () {
          if (!confirm('Удалить портлет?')) {
            return;
          }

          this.trigger('clear');
          that.update();
        },
      });

      return new RShPortlet(options);
    },

    getData: function () {
      var data = this.$area
        .find('.rsh-portlet')
        .map(function () {
          var portlet = $(this).data('rsh.portlet');

          if (portlet) {
            var attachment = portlet.data.attachment;

            if (attachment && attachment.id) {
              return $.extend({}, portlet.data, {
                attachment: attachment.sizes.full.url,
              });
            }

            return portlet.data;
          }

          return false;
        })
        .get()
        .filter(function (data) {
          return data &&
            (data.title || data.text || data.attachment);
        });

      return JSON.stringify(data);
    },
  });

  $.extend(api.controlConstructor, {
    rsh_editor: api.RShEditorControl,
    rsh_link: api.RShLinkControl,
    rsh_portlets: api.RShPortletsControl,
  });

})(jQuery, wp.customize);

