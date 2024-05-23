"use strict";
(() => {
  // src/assets/js/fields/button.field.ts
  var ButtonField = (framework) => {
    framework.querySelectorAll(".wpcf-button").forEach(async (button) => {
      button.addEventListener("click", async function(event) {
        event.preventDefault();
        const action = button.getAttribute("data-action");
        const data = button.getAttribute("data-data");
        const message = button.getAttribute("data-message");
        if (!action) {
          return;
        }
        const self = this;
        try {
          button.classList.add("wpcf-loading");
          const response = await fetch(wpcf.ajaxUrl, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
            },
            body: new URLSearchParams({
              action,
              data,
              nonce: wpcf.nonce
            })
          });
          console.log(response);
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          const responseData = await response.json();
          if (wpcf.debug) {
            console.log(responseData);
          }
          if (message && responseData.data !== void 0) {
            const style = responseData.success ? "updated" : "error";
            const messageDiv = document.createElement("div");
            messageDiv.classList.add("wpcf-button-message", style);
            messageDiv.innerHTML = `<p>${responseData.data}</p>`;
            button.after(messageDiv);
            setTimeout(() => {
              messageDiv.style.opacity = "0";
            }, 3e3);
            setTimeout(() => {
              messageDiv.remove();
            }, 3500);
          }
        } catch (error) {
          if (wpcf.debug) {
            console.error("Error:", error);
          }
        } finally {
          button.classList.remove("wpcf-loading");
        }
      });
    });
  };

  // src/assets/js/fields/code.field.ts
  var CodeField = (framework) => {
    if (typeof wp.codeEditor === "undefined") {
      return;
    }
    framework.querySelectorAll(".wpcf-code-editor-value").forEach((node) => {
      const settings = JSON.parse(node.dataset.settings || "{}");
      window.wpcfCodeMirror[node.id] = wp.codeEditor.initialize(node, settings);
    });
  };

  // src/assets/js/fields/colorpicker.field.ts
  var ColorpickerField = (framework) => {
    const colorpickers = framework.querySelectorAll(".wpcf-colorpicker");
    colorpickers.forEach((element) => {
      jQuery(element).wpColorPicker({
        palettes: true
      });
    });
  };

  // src/assets/js/fields/datepicker.field.ts
  var DatepickerField = (framework) => {
    if (typeof flatpickr !== "function") {
      return;
    }
    const config = {
      altFormat: "F j, Y",
      altInput: true,
      dateFormat: "U",
      time_24hr: true,
      wrap: true
    };
    const datePicker = framework.querySelectorAll(".wpcf-datepicker");
    datePicker.forEach((element) => {
      const customProperties = ["enable-time", "alt-format", "date-format", "locale", "max-date", "min-date", "mode", "no-calendar", "week-numbers"];
      customProperties.forEach((attribute) => {
        const propertyValue = element.dataset[attribute];
        if (propertyValue) {
          const propertyName = attribute.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
          config[propertyName] = propertyValue;
        }
      });
      flatpickr(element, config);
    });
  };

  // src/assets/js/fields/heading.field.ts
  var HeadingField = () => {
    const collapsibleElements = document.querySelectorAll(".wpcf-heading-collapsible");
    console.log(collapsibleElements);
    collapsibleElements.forEach((element) => {
      const collapsibleSections = element.dataset.sections;
      if (!collapsibleSections) {
        return;
      }
      const sectionsArray = collapsibleSections.split(",");
      sectionsArray.forEach((section) => {
        document.querySelector(`li[id$="${section}"]`)?.classList.add("hidden");
        document.querySelector(`.wpcf-field.field-id-${section}`)?.classList.add("hidden");
      });
      element.addEventListener("click", () => {
        element.classList.toggle("active");
        sectionsArray.forEach((section) => {
          document.querySelector(`li[id$="${section}"]`)?.classList.toggle("hidden");
          document.querySelector(`.wpcf-field.field-id-${section}`)?.classList.toggle("hidden");
        });
      });
    });
  };

  // src/assets/js/fields/icons.field.ts
  var IconsField = (framework) => {
    const searchFields = framework.querySelectorAll(".wpcf-icons-search");
    const iconNodes = {};
    searchFields.forEach((searchField) => {
      searchField.addEventListener("input", (event) => {
        const fieldId = searchField.closest(".wpcf-field").dataset.id;
        if (!fieldId) {
          return;
        }
        if (!iconNodes[fieldId]) {
          iconNodes[fieldId] = document.querySelectorAll(`[data-id="${fieldId}"] .wpcf-icon-list li`);
        }
        iconNodes[fieldId].forEach((icon) => {
          if (!searchField.value) {
            icon.classList.remove("hidden");
            return;
          }
          if (icon.dataset.icon && icon.dataset.icon.includes(searchField.value)) {
            icon.classList.remove("hidden");
          } else {
            icon.classList.add("hidden");
          }
        });
      });
    });
  };

  // src/assets/js/fields/location.field.ts
  var LocationField = (framework) => {
    framework.querySelectorAll(".wpcf-location").forEach((locationElement) => {
      const searchInput = locationElement.querySelector(".wpcf-map-search"), mapCanvas = locationElement.querySelector(".wpcf-map-canvas"), latitude = locationElement.querySelector(".latitude"), longitude = locationElement.querySelector(".longitude"), city = locationElement.querySelector(".city"), country = locationElement.querySelector(".country"), zip = locationElement.querySelector(".postal_code"), street = locationElement.querySelector(".street"), state = locationElement.querySelector(".state"), number = locationElement.querySelector(".number");
      let latLng = new google.maps.LatLng(52.2129918, 5.2793703), zoom = 7;
      if (latitude.value && longitude.value) {
        latLng = new google.maps.LatLng(parseFloat(latitude.value), parseFloat(longitude.value));
        zoom = 15;
      }
      const mapOptions = {
        scrollwheel: false,
        center: latLng,
        zoom,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      };
      const map = new google.maps.Map(mapCanvas, mapOptions);
      const markerOptions = {
        map,
        draggable: false
      };
      const marker = new google.maps.Marker(markerOptions);
      const autocomplete = new google.maps.places.Autocomplete(searchInput, {
        types: ["geocode"]
      });
      if (latitude.value.length > 0 && longitude.value.length > 0) {
        marker.setPosition(latLng);
      }
      autocomplete.bindTo("bounds", map);
      google.maps.event.addListener(autocomplete, "place_changed", () => {
        const place = autocomplete.getPlace();
        const components = place.address_components;
        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        latitude.value = place.geometry.location.lat().toString();
        longitude.value = place.geometry.location.lng().toString();
        if (components) {
          for (let i = 0; i < components.length; i++) {
            const component = components[i];
            const types = component.types;
            if (types.includes("street_number")) {
              number.value = component.long_name;
            } else if (types.includes("route")) {
              street.value = component.long_name;
            } else if (types.includes("locality")) {
              city.value = component.long_name;
            } else if (types.includes("postal_code")) {
              zip.value = component.long_name;
            } else if (types.includes("administrative_area_level_1")) {
              state.value = component.long_name;
            } else if (types.includes("country")) {
              country.value = component.long_name;
            }
          }
        }
      });
    });
  };

  // src/assets/js/fields/media.field.ts
  var MediaField = (framework) => {
    const uploadWrappers = framework.querySelectorAll(".wpcf-upload-wrapper");
    uploadWrappers.forEach((uploadWrapper) => {
      const addMedia = uploadWrapper.querySelector(".wpcf-upload-add");
      const addWrap = uploadWrapper.querySelector(".wpcf-single-media.empty");
      const button = uploadWrapper.dataset.button;
      const multiple = uploadWrapper.dataset.multiple === "true";
      const title = uploadWrapper.dataset.title;
      const type = uploadWrapper.dataset.type;
      const url = uploadWrapper.dataset.url;
      const valueInput = uploadWrapper.querySelector(".wpcf-upload-value");
      let frame;
      addMedia.addEventListener("click", (e) => {
        e.preventDefault();
        if (frame) {
          frame.open();
          return;
        }
        frame = wp.media({
          title,
          library: {
            type
          },
          button: {
            text: button
          },
          multiple
        });
        frame.on("select", () => {
          const attachments = frame.state().get("selection").toJSON();
          let attachmentIds = valueInput.value;
          let urlWrapper = "";
          let src;
          attachments.forEach((attachment) => {
            attachmentIds += attachment.id + ",";
            src = attachment.type === "image" ? attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.sizes.full.url : attachment.icon;
            if (url) {
              urlWrapper = '<div class="wpcf-media-url"><i class="material-icons">link</i><input type="text" value="' + attachment.url + '"></div>';
            }
            addWrap.insertAdjacentHTML("beforebegin", '<div class="wpcf-single-media type-' + type + '" data-id="' + attachment.id + '"><img src="' + src + '" />' + urlWrapper + '<a href="#" class="wpcf-upload-remove"><i class="material-icons">clear</i></a></div>');
          });
          if (!multiple) {
            attachmentIds.replace(",", "");
          }
          valueInput.value = attachmentIds;
        });
        frame.open();
      });
      uploadWrapper.addEventListener("click", (event) => {
        const target = event.target;
        if (!target.classList.contains("wpcf-upload-remove") || target.parentElement?.classList.contains("wpcf-upload-remove")) {
          return;
        }
        event.preventDefault();
        const singleMedia = target.closest(".wpcf-single-media");
        ;
        const targetId = singleMedia.dataset.id;
        let currentValues = valueInput.value;
        const newValues = currentValues.replace(targetId + ",", "");
        singleMedia.remove();
        if (!multiple) {
          jQuery(addWrap).fadeIn();
        }
        valueInput.value = newValues;
      });
    });
    jQuery(".wpcf-media").sortable({
      placeholder: "wpcf-media-highlight",
      update: function(event, ui) {
        const targetElement = event.target;
        const inputElement = targetElement.closest(".wpcf-upload-wrapper").querySelector(".wpcf-upload-value");
        const values = [];
        event.target.querySelectorAll(".wpcf-single-media").forEach((node) => {
          values.push(node.dataset.id || "");
        });
        inputElement.value = values.join(",");
      }
    });
  };

  // src/assets/js/fields/repeatable.field.ts
  var RepeatableField = (framework) => {
    jQuery(".wpcf-repeatable-groups").sortable({
      placeholder: "wpcf-highlight",
      update: function(event, ui) {
      }
    });
    document.querySelectorAll(".wpcf-repeatable-add").forEach((button) => {
      const repeatableGroup = button.closest(".wpcf-repeatable-container");
      button.addEventListener("click", (e) => {
        e.preventDefault();
        const codeNodes = [];
        const length = repeatableGroup.querySelectorAll(".wpcf-repeatable-group").length;
        const group = repeatableGroup.querySelector(".wpcf-repeatable-group:last-child");
        const selectAdvancedFields = group.querySelectorAll(".wpcf-select-advanced");
        selectAdvancedFields.forEach((field) => {
          jQuery(field).select2("destroy");
        });
        repeatableGroup.querySelectorAll(".wpcf-code-editor-value").forEach((node) => {
          if (window.wpcfCodeMirror[node.id]) {
            window.wpcfCodeMirror[node.id].codemirror.toTextArea(node);
            codeNodes.push(node);
          }
        });
        const datepickers = group.querySelectorAll(".wpcf-datepicker");
        datepickers.forEach((datepickerInstance) => {
          if (datepickerInstance._flatpickr) {
            datepickerInstance._flatpickr.destroy();
          }
        });
        const newGroup = group.cloneNode(true);
        newGroup.innerHTML = newGroup.innerHTML.replace(/\[\d+\]/g, `[${length}]`).replace(/\_\d+\_/g, `_${length}_`);
        newGroup.querySelectorAll("input, textarea").forEach((input) => input.value = "");
        newGroup.querySelectorAll("option").forEach((option) => option.selected = false);
        newGroup.querySelectorAll(".wpcf-single-media:not(.empty)").forEach((media) => media.remove());
        group.after(newGroup);
        FieldsModule(newGroup, true);
        datepickers.forEach((element) => {
          DatepickerField(group);
        });
        codeNodes.forEach((node) => {
          const settings = JSON.parse(node.dataset.settings);
          window.wpcfCodeMirror[node.id] = wp.codeEditor.initialize(node, settings);
        });
      });
    });
    document.querySelectorAll(".wpcf-repeatable-remove-latest").forEach((button) => {
      button.addEventListener("click", (e) => {
        e.preventDefault();
        const groupLength = button.closest(".wpcf-repeatable-container").querySelectorAll(".wpcf-repeatable-group").length;
        const group = button.closest(".wpcf-repeatable-container").querySelector(".wpcf-repeatable-group:last-child");
        if (groupLength < 2) {
          return;
        }
        jQuery(group).fadeOut(350, () => group.remove());
      });
    });
    document.addEventListener("click", (e) => {
      const target = e.target;
      if (target.classList.contains("wpcf-repeatable-remove-group") || target.closest("a")?.classList.contains("wpcf-repeatable-remove-group")) {
        e.preventDefault();
        const groupLength = target.closest(".wpcf-repeatable-container").querySelectorAll(".wpcf-repeatable-group").length;
        const group = target.closest(".wpcf-repeatable-group");
        if (groupLength < 2) {
          return;
        }
        jQuery(group).fadeOut(350, () => group.remove());
      }
    });
    document.querySelectorAll(".wpcf-repeatable-toggle").forEach((button) => {
      button.addEventListener("click", (e) => {
        e.preventDefault();
        const icon = button.querySelector("i");
        const group = button.closest(".wpcf-repeatable-group").querySelector(".wpcf-repeatable-fields");
        if (icon.textContent === "arrow_drop_down") {
          icon.textContent = "arrow_drop_up";
        } else if (icon.textContent === "arrow_drop_up") {
          icon.textContent = "arrow_drop_down";
        }
        group.style.display = group.style.display === "none" ? "block" : "none";
      });
    });
  };

  // src/assets/js/fields/select.field.ts
  var SelectField = () => {
    if (typeof jQuery.fn.select2 !== "undefined" && jQuery.fn.select2) {
      jQuery(".wpcf-select-advanced").select2({});
      jQuery(".wpcf-typography-fonts").select2({
        templateResult: formatState,
        templateSelection: formatState
      });
    }
  };
  var formatState = (state) => {
    if (!state.id) {
      return state.text;
    }
    const newState = jQuery(
      '<img src="' + state.element.dataset.display + '" class="img-typography" />'
    );
    return newState;
  };

  // src/assets/js/fields/slider.field.ts
  var SliderField = (framework) => {
    framework.querySelectorAll(".wpcf-slider-input").forEach((slider) => {
      const sliderValueElement = slider.nextElementSibling;
      slider.addEventListener("input", (event) => {
        sliderValueElement.innerHTML = event?.target?.value;
      });
    });
  };

  // src/assets/js/helpers/dependency.helper.ts
  var DependencyHelper = (framework) => {
    framework.querySelectorAll(".wpcf-dependent-field").forEach((item) => {
      const field = item.classList.contains("wpcf-repeatable-field") ? item.querySelector(".wpcf-repeatable-field-input") : item.querySelector(".wpcf-field-input");
      const equation = field?.getAttribute("data-equation");
      const source = field?.getAttribute("data-source");
      const value = field?.getAttribute("data-value");
      if (!equation || !source || !value) {
        return;
      }
      const selector = item.classList.contains("wpcf-repeatable-field") ? ".wpcf-repeatable-group" : ".wpcf-fields";
      const target = item.closest(selector)?.querySelector(`.field-id-${source}`);
      const input = target?.querySelector("input");
      const select = target?.querySelector("select");
      if (select) {
        jQuery(select).on("change", function() {
          compare(this, item, equation, value);
        });
      }
      if (input) {
        jQuery(input).on("change", function() {
          compare(this, item, equation, value);
        });
      }
    });
    function compare(changedField, dependentField, equation, value) {
      let changedFieldValue = changedField.value;
      if (changedField.type === "checkbox") {
        changedField = changedField;
        if (changedField.checked && changedField.dataset.key === value) {
          changedFieldValue = value;
        } else if (!changedField.checked && changedField.dataset.key === value) {
          changedFieldValue = "";
        }
      }
      if (equation === "=") {
        if (changedFieldValue === value) {
          dependentField.classList.add("active");
        } else {
          dependentField.classList.remove("active");
        }
      }
      if (equation === "!=") {
        if (changedFieldValue !== value) {
          dependentField.classList.add("active");
        } else {
          dependentField.classList.remove("active");
        }
      }
    }
  };

  // src/assets/js/modules/fields.module.ts
  var FieldsModule = (framework, isRepeatable = false) => {
    setTimeout(() => {
      HeadingField();
      SelectField();
    }, 10);
    if (!framework) {
      return;
    }
    ButtonField(framework);
    CodeField(framework);
    ColorpickerField(framework);
    DatepickerField(framework);
    IconsField(framework);
    LocationField(framework);
    MediaField(framework);
    SliderField(framework);
    DependencyHelper(framework);
    if (!isRepeatable) {
      RepeatableField(framework);
    }
  };

  // src/assets/js/layout/options.layout.ts
  var OptionsLayout = (framework) => {
    if (!framework.classList.contains("wpcf-options-page")) {
      return;
    }
    const scrollHeader = framework.querySelector(".wpcf-notifications");
    const scrollWidth = scrollHeader.offsetWidth;
    let scrollPosition = 0;
    window.addEventListener("scroll", () => {
      scrollPosition = window.scrollY;
      if (scrollPosition > 50) {
        scrollHeader.style.width = `${scrollWidth}px`;
        scrollHeader.closest(".wpcf-header")?.classList.add("wpfc-header-scrolling");
      } else {
        scrollHeader.closest(".wpcf-header")?.classList.remove("wpfc-header-scrolling");
      }
    });
  };

  // src/assets/js/layout/tabs.layout.ts
  var TabsLayout = function() {
    const tabs = document.querySelectorAll(".wpcf-tabs a");
    tabs.forEach((tab) => {
      tab.addEventListener("click", function(e) {
        e.preventDefault();
        const activeTab = this.getAttribute("href");
        const section = activeTab.replace("#", "");
        const frame = this.closest(".wpcf-framework").id;
        const customFieldsSection = document.querySelector(`#wp_custom_fields_section_${frame}`);
        if (customFieldsSection) {
          customFieldsSection.value = section;
        }
        const framework = this.closest(".wpcf-framework");
        if (framework) {
          framework.querySelectorAll(".wpcf-tabs a").forEach((tab2) => tab2.classList.remove("active"));
          framework.querySelectorAll(".wpcf-section").forEach((section2) => section2.classList.remove("active"));
        }
        this.classList.add("active");
        const newActiveTab = document.querySelector(activeTab);
        if (newActiveTab) {
          newActiveTab.classList.add("active");
        }
      });
    });
  };

  // src/assets/js/app.ts
  var InitWPCF = () => {
    const framework = document.querySelector(".wpcf-framework") ?? void 0;
    window.wpcfCodeMirror = {};
    FieldsModule(framework);
    if (framework) {
      OptionsLayout(framework);
    }
    TabsLayout();
  };
  document.addEventListener("DOMContentLoaded", () => InitWPCF());
})();
