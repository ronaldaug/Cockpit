
<vue-view class="kiss-margin-small">

    <template>

        <kiss-container>

            <ul class="kiss-breadcrumbs">
                <li><a href="<?=$this->route('/content')?>"><?=t('Content')?></a></li>
            </ul>

            <div class="kiss-flex kiss-flex-middle">
                <div class="kiss-flex kiss-position-relative">
                    <span class="kiss-badge" style="<?=($model['color'] ? "background:{$model['color']};border-color:{$model['color']}":"")?>"><?=$this->escape($model['label'] ? $model['label'] : $model['name'])?></span>
                    <a class="kiss-cover" href="<?=$this->route("/content/collection/items/{$model['name']}")?>"></a>
                </div>
                <div class="kiss-margin-small-left kiss-size-5 kiss-text-bold">
                    <span v-if="!item._id"><?=t('New Item')?></span>
                    <span v-if="item._id"><?=t('Edit Item')?></span>
                </div>
                <a class="kiss-size-large kiss-margin-small-left" kiss-popoutmenu="#model-item-menu-actions"><icon>more_horiz</icon></a>
            </div>
        </kiss-container>

        <kiss-container class="kiss-margin-large">

            <kiss-card class="kiss-margin-large kiss-size-5 kiss-align-center kiss-color-muted kiss-text-bolder kiss-padding-large" theme="bordered" v-if="!fields.length">
                <?=t('No fields defined')?>
            </kiss-card>

            <kiss-row class="kiss-margin-large" gap="large" v-if="fields.length">
                <div class="kiss-flex-1">
                    <div class="kiss-width-2-3@xl kiss-margin-auto">
                        <fields-renderer v-model="item" :fields="fields" :locales="locales"></fields-renderer>
                    </div>
                </div>
                <div class="kiss-width-1-4@m kiss-width-1-5@xl">

                    <div class="kiss-margin" v-if="item._id">

                        <div class="kiss-text-caption kiss-size-xsmall kiss-text-bold">{{ t('Document') }}</div>

                        <kiss-card class="kiss-margin-small kiss-bgcolor-contrast kiss-padding-small">

                            <div class="kiss-margin-xsmall">
                                <div class="kiss-flex kiss-flex-middle">
                                    <div class="kiss-size-4 kiss-margin-small-right kiss-flex" title="ID"><icon>adjust</icon></div>
                                    <div class="kiss-text-truncate kiss-text-bold kiss-text-monospace kiss-size-small kiss-flex-1">{{ item._id }}</div>
                                    <a :title="t('Copy')" @click="copyID()"><icon>copy</icon></a>
                                </div>
                            </div>

                            <div class="kiss-margin-xsmall">
                                <div class="kiss-flex kiss-flex-middle">
                                    <div class="kiss-size-4 kiss-margin-small-right kiss-flex kiss-color-muted" :title="t('Created at')"><icon>more_time</icon></div>
                                    <div class="kiss-text-truncate kiss-size-small kiss-text-monospace kiss-color-muted kiss-flex-1">{{ (new Date(item._created * 1000).toLocaleString()) }}</div>
                                    <div><icon>account_circle</icon></div>
                                </div>
                            </div>

                            <div class="kiss-margin-xsmall" v-if="item._created != item._modified">
                                <div class="kiss-flex kiss-flex-middle">
                                    <div class="kiss-size-4 kiss-margin-small-right kiss-flex kiss-color-muted" :title="t('Modified at')"><icon>history</icon></div>
                                    <div class="kiss-text-truncate kiss-size-small kiss-text-monospace kiss-color-muted kiss-flex-1">{{ (new Date(item._modified * 1000).toLocaleString()) }}</div>
                                    <div><icon>account_circle</icon></div>
                                </div>
                            </div>

                        </kiss-card>
                    </div>

                    <div class="kiss-margin">

                        <div class="kiss-text-caption kiss-size-xsmall kiss-text-bold">{{ t('State') }}</div>

                        <div class="kiss-margin-small">
                            <button type="button" class="kiss-button kiss-flex kiss-flex-middle kiss-width-expand kiss-align-left" :class="{'kiss-bgcolor-danger': !item._state, 'kiss-bgcolor-success': item._state == 1}" kiss-popoutmenu="#model-item-menu-state">
                                <span class="kiss-flex-1" v-if="item._state == 1">{{ t('Published') }}</span>
                                <span class="kiss-flex-1" v-if="!item._state">{{ t('Unpublished') }}</span>
                                <span class="kiss-flex-1" v-if="item._state == -1">{{ t('Archive') }}</span>
                                <icon>expand_more</icon>
                            </button>
                        </div>

                    </div>

                    <div class="kiss-margin" v-if="hasLocales">

                        <div class="kiss-text-caption kiss-size-xsmall kiss-text-bold">{{ t('Translation') }}</div>

                        <kiss-card class="kiss-padding-small kiss-margin-small kiss-text-muted kiss-size-small kiss-color-muted kiss-flex kiss-flex-middle" theme="bordered" v-if="!locales.length">
                            <span class="kiss-flex-1 kiss-margin-small-right">{{ t('No locales.') }}</span>
                            <a class="kiss-size-xsmall  kiss-text-bolder" href="<?=$this->route('/system/locales')?>">{{ t('Manage') }}</a>
                        </kiss-card>

                        <div class="kiss-margin-small" v-if="locales.length">

                            <kiss-card class="kiss-position-relative kiss-padding-small kiss-margin-small kiss-text-bolder kiss-flex kiss-flex-middle" :class="{'kiss-color-muted': !loc.visible}" :theme="!loc.visible ? 'bordered':'bordered contrast'" v-for="loc in locales">
                                <icon class="kiss-margin-small-right" :class="{'kiss-color-primary': loc.visible}">{{ loc.visible ? 'visibility' : 'visibility_off' }}</icon>
                                <span class="kiss-size-small kiss-flex-1">{{ loc.name }}</span>
                                <span class="kiss-color-muted kiss-size-xsmall" v-if="loc.i18n == 'default'">{{ t('Default') }}</span>
                                <a class="kiss-cover" @click="loc.visible = !loc.visible"></a>
                            </kiss-card>
                        </div>

                    </div>

                    <div class="kiss-margin kiss-visible@m" v-if="model.preview && model.preview.length">

                        <div class="kiss-text-caption kiss-size-xsmall kiss-text-bold kiss-margin-small-bottom">{{ t('Content preview') }}</div>

                        <a class="kiss-button kiss-width-1-1" kiss-popoutmenu="#model-item-preview-links" v-if="model.preview.length > 1">{{ t('Open preview') }}</a>
                        <a class="kiss-button kiss-width-1-1" @click="showPreviewUri(model.preview[0].uri)" v-if="model.preview.length == 1">{{ t('Open preview') }}</a>
                    </div>

                </div>
            </kiss-row>
        </kiss-container>

        <app-actionbar>

            <kiss-container>
                <div class="kiss-flex kiss-flex-middle">
                    <div class="kiss-button-group" v-if="item._id">
                        <a class="kiss-button" href="<?=$this->route("/content/collection/item/{$model['name']}")?>">
                            <?=t('Create new item')?>
                        </a>
                    </div>
                    <div class="kiss-flex-1"></div>
                    <div class="kiss-button-group">
                        <a class="kiss-button" href="<?=$this->route("/content/collection/items/{$model['name']}")?>">
                            <span v-if="!item._id"><?=t('Cancel')?></span>
                            <span v-if="item._id"><?=t('Close')?></span>
                        </a>
                        <a class="kiss-button kiss-button-primary" @click="save()">
                            <span v-if="!item._id"><?=t('Create item')?></span>
                            <span v-if="item._id"><?=t('Update item')?></span>
                        </a>
                    </div>
                </div>
            </kiss-container>

        </app-actionbar>

        <kiss-popoutmenu id="model-item-menu-state">
            <kiss-content>
                <kiss-navlist class="kiss-margin">
                    <ul>
                        <li class="kiss-nav-header"><?=t('Change state to')?></li>
                        <li v-show="item._state != 1">
                            <a class="kiss-flex kiss-flex-middle kiss-color-success kiss-text-bold" @click="item._state=1">
                                <icon class="kiss-margin-small-right">bookmark</icon>
                                <?=t('Published')?>
                            </a>
                        </li>
                        <li v-show="item._state">
                            <a class="kiss-flex kiss-flex-middle kiss-color-danger kiss-text-bold" @click="item._state=0">
                                <icon class="kiss-margin-small-right">bookmark</icon>
                                <?=t('Unpublished')?>
                            </a>
                        </li>
                        <li v-show="item._state != -1">
                            <a class="kiss-flex kiss-flex-middle kiss-color-muted kiss-text-bold" @click="item._state=-1">
                                <icon class="kiss-margin-small-right">bookmark</icon>
                                <?=t('Archive')?>
                            </a>
                        </li>
                    </ul>
                </kiss-navlist>
            </kiss-content>
        </kiss-popoutmenu>

        <kiss-popoutmenu id="model-item-menu-actions">
            <kiss-content>
                <kiss-navlist class="kiss-margin">
                    <ul>
                        <li class="kiss-nav-header"><?=t('Actions')?></li>
                        <li>
                            <a class="kiss-flex kiss-flex-middle" @click="showJSON()">
                                <icon class="kiss-margin-small-right">manage_search</icon>
                                <?=t('Json Object')?>
                            </a>
                        </li>
                        <li v-if="item._id">
                            <a class="kiss-flex kiss-flex-middle" href="<?=$this->route("/content/collection/item/{$model['name']}")?>">
                                <icon class="kiss-margin-small-right">add_circle_outline</icon>
                                <?=t('Create new item')?>
                            </a>
                        </li>
                        <li class="kiss-nav-divider"></li>
                        <li>
                            <a class="kiss-flex kiss-flex-middle" href="<?=$this->route("/content/models/edit/{$model['name']}")?>">
                                <icon class="kiss-margin-small-right">create</icon>
                                <?=t('Edit model')?>
                            </a>
                        </li>
                    </ul>
                </kiss-navlist>
            </kiss-content>
        </kiss-popoutmenu>

        <kiss-popoutmenu id="model-item-preview-links" v-if="model.preview && model.preview.length">
            <kiss-content>
                <kiss-navlist class="kiss-margin">
                    <ul>
                        <li class="kiss-nav-header"><?=t('Open preview')?></li>
                        <li v-for="preview in model.preview">
                            <a class="kiss-flex kiss-flex-middle" @click="showPreviewUri(preview.uri)">
                                <icon class="kiss-margin-small-right">travel_explore</icon>
                                {{ preview.name }}
                            </a>
                        </li>
                    </ul>
                </kiss-navlist>
            </kiss-content>
        </kiss-popoutmenu>

    </template>

    <script type="module">

        export default {
            data() {
                return {
                    model: <?=json_encode($model)?>,
                    item: <?=json_encode($item)?>,
                    fields: <?=json_encode($fields)?>,
                    locales: <?=json_encode($locales)?>,
                    saving: false,
                    isModified: false
                }
            },

            components: {
                'fields-renderer': 'system:assets/vue-components/fields-renderer.js',
                'json-viewer': 'system:assets/vue-components/json-viewer.js',
            },

            computed: {
                hasLocales() {

                    for (let i=0;i<this.fields.length;i++) {
                        if (this.fields[i].i18n) return true;
                    }
                    return false;
                }
            },

            created() {

                window.onbeforeunload = e => {

                    if (this.isModified) {
                        e.preventDefault();
                        e.returnValue = this.t('You have unsaved data! Are you sure you want to leave?');
                    }
                };
            },

            watch: {
                item: {
                    handler() {
                        //this.isModified = true;
                    },
                    deep: true
                }
            },

            methods: {

                save() {

                    let validate = {root: this.$el.parentNode};

                    App.trigger('fields-renderer-validate', validate);

                    if (validate.errors) {
                        return;
                    }

                    let model = this.model.name;

                    this.saving = true;

                    this.$request(`/content/models/saveItem/${model}`, {item: this.item}).then(item => {

                        this.item = Object.assign(this.item, item);
                        this.saving = false;
                        App.ui.notify('Data updated!');

                        this.$nextTick(() => this.isModified = false);

                    }).catch(rsp => {
                        this.saving = false;
                        App.ui.notify(rsp.error || 'Saving failed!', 'error');
                    });
                },

                copyID() {
                    App.utils.copyText(this.item._id, () =>  App.ui.notify('ID copied!'));
                },

                showJSON() {
                    VueView.ui.offcanvas('system:assets/dialogs/json-viewer.js', {data: this.item}, {}, {flip: true, size: 'large'})
                },

                showPreviewUri(uri) {

                    VueView.ui.offcanvas('system:assets/dialogs/content-preview.js', {
                        uri,
                        fields: this.model.fields,
                        item: this.item,
                        locales: this.hasLocales ? this.locales : [],
                        context: {
                            model: this.model.name
                        }
                    }, {
                        update: (item) => {
                            this.item = Object.assign(this.item, item);
                        }
                    }, {size: 'screen'})
                }
            }
        }
    </script>

</vue-view>