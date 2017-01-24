CityUpdater = Class.create();
CityUpdater.prototype = {
    initialize: function (countryEl, regionEl, cityTextEl, citySelectEl, cities) {
        console.log('initialized');
        this.regionEl = $(regionEl);
        this.cityTextEl = $(cityTextEl);
        this.citySelectEl = $(citySelectEl);
        this.cities = cities;
        this.countryEl = $(countryEl);
        //if (this.citySelectEl.options.length<=1) {
        this.update();
        //	}

        this.regionEl.changeUpdater = this.update.bind(this);

        Event.observe(this.regionEl, 'change', this.update.bind(this));
        Event.observe(this.countryEl, 'change', this.update.bind(this));
        Event.observe(this.citySelectEl, 'change', this.updateCity.bind(this));
    },
    update: function () {
        if (this.cities[this.regionEl.value]) {
            var i, option, city, def;
            def = this.citySelectEl.getAttribute('defaultValue');
            if (this.cityTextEl) {
                if (!def) {
                    def = this.cityTextEl.value.toLowerCase();
                }
                ////need to comment this to avoid issue when saving address without touching city field
                //this.cityTextEl.value = '';
            }

            this.citySelectEl.options.length = 1;

            var cities = [];

            for (cityId in this.cities[this.regionEl.value]) {
                city = this.cities[this.regionEl.value][cityId];
                cities.push({
                   id: cityId,
                   name: city.name
                });
            }
            
            cities.sort(function (a, b) {
                return a.name.localeCompare(b.name);
            });
            
            for (var i = 0; i < cities.length; i++) {
                var cityId = cities[i].id;
                city = this.cities[this.regionEl.value][cityId];

                option = document.createElement('OPTION');
                option.value = city.code;
                option.text = city.name.stripTags();
                option.title = city.name;

                if (this.citySelectEl.options.add) {
                    this.citySelectEl.options.add(option);
                } else {
                    this.citySelectEl.appendChild(option);
                }

                if (cityId == def || (city.name && city.name == def) ||
                        (city.name && city.code.toLowerCase() == def)
                        ) {
                    this.citySelectEl.value = city.code;
                }
            }

            if (this.cityTextEl) {
                this.cityTextEl.style.display = 'none';
            }
            this.citySelectEl.style.display = '';
        } else {
            this.citySelectEl.options.length = 1;
            if (this.cityTextEl) {
                this.cityTextEl.style.display = '';
            }
            this.citySelectEl.style.display = 'none';
            Validation.reset(this.citySelectEl);
        }
   },
    updateCity: function () {
        var sIndex = this.citySelectEl.selectedIndex;
        this.cityTextEl.value = this.citySelectEl.options[sIndex].value;
    }
}

function reloadRegion(countryId, regionUrl) {
    loader = new varienLoader(true);
    loader.load(regionUrl, {country_id: countryId}, onReloadRegion.bind(this));
}

function onReloadRegion(serverResponse) {
    if (!serverResponse)
        return;
    var data = eval('(' + serverResponse + ')');
    var regionSelect = $('region_id');
    if (regionSelect && data.success) {
        regionSelect.update(data.options)
    }
}