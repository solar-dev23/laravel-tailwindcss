<template>
    <div
        v-if="selected"
        class="fixed min-h-screen md:min-h-0 md:max-h-screen bottom-0 top-0 right-0 w-full md:w-3/4 bg-white shadow-2xl p-5 md:my-12 z-50 block text-sm"
    >
        <button
            @click="closeSelected"
            class="md:absolute rounded-full bg-blue-500 hover:bg-blue-800 text-white md:-ml-5 md:-mt-5 top-0 left-0 shadow-xl hover:shadow-lg"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                class="h-10 fill-current"
            >
                <path d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"
                />
            </svg>
        </button>

        <div class="overflow-y-auto md:max-h-full pb-20">
            <form @submit.prevent="submit">
                <div
                    class="w-full absolute bottom-0 left-0 right-0 bg-white shadow p-2 z-10 border-t border-blue-500"
                >
                    <loading-button
                        :loading="sending"
                        class="btn btn-primary"
                        type="submit"
                        :disabled="sending"
                        >{{ buttonTitle }}</loading-button
                    >
                </div>
                <div class="flex">
                    <div class="md:w-1/2 md:px-5">
                        <div class="border-b py-2">
                            <video-player
                                v-if="selected"
                                :src="selected.preview"
                                :poster="selected.thumbnail"
                                :id="selected.file_uuid"
                            />
                        </div>
                        <div class="border-b py-2">
                            <h3 class="font-bold mb-2">
                                Video MetaInformation
                            </h3>
                            <video-metainfo :video="selected" display-title />
                        </div>
                    </div>
                    <div class="md:w-1/2 md:px-5">
                        <div class="py-2">
                            <text-input
                                v-model="selected.title"
                                :errors="$page.errors.title"
                                class="mt-5"
                                label="Title"
                                type="text"
                                autofocus
                                placeholder="Video Title"
                                autocapitalize="off"
                            />
                        </div>
                        <div class="mt-5">
                            <label class="form-label" for="description"
                                >Description:</label
                            >
                            <textarea
                                v-model="selected.description"
                                id="description"
                                class="form-input"
                                placeholder="Add video description."
                                :class="{ error: $page.errors.description }"
                            >
                            </textarea>
                            <div
                                v-if="$page.errors.description"
                                class="form-error"
                            >
                                {{ $page.errors.description[0] }}
                            </div>
                        </div>
                        <div class="mt-5">
                            <label class="form-label" for="keywords"
                                >Keywords:</label
                            >
                            <tags-input
                                element-id="keywords"
                                v-model="selected.keywords"
                                placeholder="Add Keywords"
                                :limit="50"
                                add-tags-on-blur
                                add-tags-on-comma
                                :class="{ error: $page.errors.keywords }"
                            ></tags-input>
                            <div
                                v-if="$page.errors.keywords"
                                class="form-error"
                            >
                                {{ $page.errors.keywords[0] }}
                            </div>
                            <small class="pt-1 text-xs text-gray-600 block"
                                >Enter keywords describing your video, can not
                                contain username (max. 50).</small
                            >
                        </div>
                        <div class="mt-5">
                            <label class="form-label" for="editorial">
                                <input
                                    type="checkbox"
                                    v-model="selected.editorial"
                                />
                                editorial
                            </label>
                            <div
                                v-if="$page.errors.editorial"
                                class="form-error"
                            >
                                {{ $page.errors.editorial[0] }}
                            </div>
                            <small class="pt-1 text-xs text-gray-600 block"
                                >Should be selected if the footage is
                                newsworthy.</small
                            >
                        </div>
                        <div class="mt-5">
                            <label class="form-label" for="nsfw">
                                <input
                                    type="checkbox"
                                    id="nsfw"
                                    v-model="selected.nsfw"
                                />
                                NSFW (Not Safe For Work)
                            </label>
                            <div v-if="$page.errors.nsfw" class="form-error">
                                {{ $page.errors.nsfw[0] }}
                            </div>
                        </div>
                        <div v-if="!selected.editorial">
                            <div class="mt-5">
                                <label class="form-label" for="People">
                                    <input
                                        type="checkbox"
                                        v-model="selected.people"
                                    />
                                    Has People
                                </label>
                                <div
                                    v-if="$page.errors.people"
                                    class="form-error"
                                >
                                    {{ $page.errors.people[0] }}
                                </div>
                                <small class="pt-1 text-xs text-gray-600 block"
                                    >Select only when people in video are model
                                    released.</small
                                >
                            </div>
                            <div class="mt-5" v-if="selected.people">
                                <text-input
                                    v-model="selected.num_people"
                                    :errors="$page.errors.num_people"
                                    class="mt-5"
                                    label="Number of People"
                                    type="text"
                                    autofocus
                                    placeholder="Number of People in video"
                                    autocapitalize="off"
                                />
                                <div
                                    v-if="$page.errors.num_people"
                                    class="form-error"
                                >
                                    {{ $page.errors.num_people[0] }}
                                </div>
                                <small class="pt-1 text-xs text-gray-600 block"
                                    >Please enter the number of people in the
                                    video which are model released.</small
                                >
                            </div>
                            <div class="mt-5">
                                <label class="form-label" for="releases"
                                    >Releases:</label
                                >
                                <tags-input
                                    element-id="releases"
                                    v-model="selected.releases"
                                    placeholder="Add Releases"
                                    typeahead
                                    typeahead-show-on-focus
                                    typeahead-style="dropdown"
                                    :existing-tags="releases"
                                    only-existing-tags
                                    :class="{ error: $page.errors.releases }"
                                ></tags-input>
                                <div
                                    v-if="$page.errors.releases"
                                    class="form-error"
                                >
                                    {{ $page.errors.releases[0] }}
                                </div>
                                <small class="pt-1 text-xs text-gray-600 block"
                                    >To attach releases they first should be
                                    uploaded.</small
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import VideoPlayer from "@/Shared/VideoPlayer";
import VoerroTagsInput from "@voerro/vue-tagsinput";
import TextInput from "@/Shared/TextInput";
import VideoMetainfo from "@/Shared/Videos/MetaInfo";
import LoadingButton from "@/Shared/LoadingButton";
export default {
    components: {
        LoadingButton,
        VideoMetainfo,
        VideoPlayer,
        TagsInput: VoerroTagsInput,
        TextInput
    },
    props: ["selected", "releases"],
    data: function() {
        return {
            sending: false
        };
    },
    computed: {
        buttonTitle: function() {
            console.log(this.selected);
            if (this.selected.status <= 1) {
                return "Submit For Review";
            } else {
                return "Update";
            }
        }
    },
    methods: {
        closeSelected: function() {
            this.$emit("close");
        },
        submit: function() {
            this.sending = true;
            this.$inertia
                .post(this.route("video.store"), this.selected)
                .then(res => {
                    this.sending = false;
                    //console.log(Object.keys(this.$page.errors).length);
                    if (Object.keys(this.$page.errors).length == 0) {
                        this.closeSelected();
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        }
    }
};
</script>

<style></style>
