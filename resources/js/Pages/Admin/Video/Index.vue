<template>
    <div class="flex">
        <card title="Video Approval Queue" class="w-full">
            <div class="md:flex justify-between">
                <ul class="w-full flex flex-1 flex-wrap items-center pb-5">
                    <li class="p-1 text-xs font-bold uppercase">
                        Filter By User:
                    </li>
                    <li
                        class="p-1"
                        v-for="contributor in contributors"
                        :key="contributor.id"
                    >
                        <inertia-link
                            :href="
                                route(
                                    'admin.videos',
                                    routeParams({
                                        contributor: contributor.uid
                                    })
                                )
                            "
                            class="btn btn-primary text-xs"
                            >{{ contributor.user.username }} ({{
                                contributor.video_count
                            }})
                        </inertia-link>
                    </li>
                </ul>
                <ul class="flex flex-wrap items-center pb-5">
                    <li class="p-1 text-xs font-bold uppercase">
                        Items:
                    </li>
                    <li class="p-1">
                        <inertia-link
                            :href="
                                route(
                                    'admin.videos',
                                    routeParams({
                                        perpage: 20
                                    })
                                )
                            "
                            class="btn btn-primary text-xs"
                            :class="{ active: perpage == 20 }"
                            >20
                        </inertia-link>
                    </li>
                    <li class="p-1">
                        <inertia-link
                            :href="
                                route(
                                    'admin.videos',
                                    routeParams({
                                        perpage: 50
                                    })
                                )
                            "
                            class="btn btn-primary text-xs"
                            :class="{ active: perpage == 50 }"
                            >50
                        </inertia-link>
                    </li>
                    <li class="p-1">
                        <inertia-link
                            :href="
                                route(
                                    'admin.videos',
                                    routeParams({
                                        perpage: 100
                                    })
                                )
                            "
                            class="btn btn-primary text-xs"
                            :class="{ active: perpage == 100 }"
                            >100
                        </inertia-link>
                    </li>
                    <li
                        v-if="selected.length"
                        class="flex text-xs items-center pl-2"
                    >
                        <strong>SELECTED:</strong>
                        <button
                            class="btn btn-primary text-xs"
                            @click="approveVideos()"
                        >
                            Accept
                        </button>
                        <button
                            class="btn btn-danger text-xs"
                            @click="rejectVideos()"
                        >
                            Reject
                        </button>
                    </li>
                </ul>
            </div>
            <table class="table table-auto flex w-full">
                <thead>
                    <tr class="border-b text-xs">
                        <th class="text-base">
                            <input
                                class="mb-1"
                                type="checkbox"
                                v-model="selectall"
                                @click="select()"
                            />
                        </th>
                        <th>Thumb/Preview</th>
                        <th>Title/Description</th>
                        <th>Keywords</th>
                        <th>Media Releases</th>
                        <th>Meta Information</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="video in videos.data"
                        :key="video.id"
                        class="border-b"
                    >
                        <td>
                            <input
                                type="checkbox"
                                :value="video.id"
                                v-model="selected"
                            />
                        </td>
                        <td class="w-56 p-1">
                            <button @click="previewVideo(video)">
                                <img :src="video.thumbnail" />
                            </button>
                        </td>
                        <td>
                            <ul class="list-none text-sm p-2">
                                <li class="mb">
                                    <strong>title:</strong> {{ video.title }}
                                </li>
                                <li class="mb">
                                    <strong>description:</strong
                                    >{{ video.description }}
                                </li>
                                <li class="mb">
                                    <strong>by:</strong
                                    >{{ video.user.username }}
                                </li>
                            </ul>
                        </td>
                        <td class="text-xs">
                            <ul
                                class="list-none flex flex-wrap p-1"
                                v-if="video.keywords.length > 0"
                            >
                                <li
                                    class="p-1 m-1 block border rounded text-blue-500 border-blue-500"
                                    v-for="keyword in video.keywords"
                                    :key="keyword"
                                >
                                    {{ keyword }}
                                </li>
                            </ul>
                            <strong class="p-1" v-else>No Keywords.</strong>
                        </td>
                        <td>
                            <ul class="text-sm">
                                <li class="p-1">
                                    <strong>people:</strong>
                                    {{ video.people ? "Yes" : "No" }}
                                </li>
                                <li class="p-1" v-if="video.num_people">
                                    <strong>number of people</strong
                                    >{{ video.num_people }}
                                </li>
                                <li
                                    class="p-1"
                                    v-for="release in video.releases"
                                    v-if="video.releases.length"
                                    :key="release.id"
                                >
                                    <a
                                        class="text-blue-500 flex items-center"
                                        :href="
                                            route('release.download', {
                                                id: release.file_uuid
                                            })
                                        "
                                        ><svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            class="fill-current w-4 inline-block mr-1"
                                        >
                                            <path
                                                d="M0 0h24v24H0z"
                                                fill="none"
                                            />
                                            <path
                                                d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"
                                            />
                                        </svg>
                                        <span> {{ release.filename }}</span></a
                                    >
                                </li>
                                <li class="p-1" v-if="video.editorial">
                                    <strong>Editorial:</strong> Yes
                                </li>
                                <li class="p-1" v-if="video.nsfw">
                                    <strong>NSFW:</strong> Yes
                                </li>
                            </ul>
                        </td>
                        <td class="md:w-1/4 p-1">
                            <video-metainfo
                                :video="video"
                                display-title
                            ></video-metainfo>
                        </td>
                        <td class="">
                            <div
                                class="flex flex-col justify-center items-center"
                            >
                                <button
                                    class="btn btn-primary text-xs"
                                    @click="approveVideos(video.id)"
                                >
                                    Accept
                                </button>
                                <button
                                    class="btn btn-danger text-xs"
                                    @click="rejectVideos(video.id)"
                                >
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="videos.links" />
        </card>
        <preview :video="preview" @preview-close="preview = null" />
    </div>
</template>

<script>
import Layout from "@/Shared/Layout";
import Card from "@/Shared/Card";
import VideoMetainfo from "@/Shared/Videos/MetaInfo";
import Preview from "./Partial/Preview";
import Pagination from "@/Shared/Pagination";
export default {
    layout: Layout,
    metaInfo: { title: "Video Approval" },
    components: {
        Card,
        VideoMetainfo,
        Preview,
        Pagination
    },
    props: {
        videos: {
            type: Object
        },
        contributors: {
            type: Array
        },
        perpage: {
            default: 20
        },
        contributor: {
            default: null
        }
    },
    data: function() {
        return {
            sending: false,
            preview: null,
            selected: [],
            selectall: false
        };
    },
    methods: {
        previewVideo: function(video) {
            this.preview = video;
        },
        routeParams: function(params) {
            let result = {};
            if (this.contributor) result.contributor = this.contributor;
            if (this.perpage) result.perpage = this.perpage;
            return Object.assign(result, params);
        },
        select: function() {
            this.selected = [];
            if (!this.selectall) {
                for (let i in this.videos.data) {
                    this.selected.push(this.videos.data[i].id);
                }
            }
        },
        approveVideos: function(id = null) {
            let selected = [];
            if (id) selected.push(id);
            else {
                selected = this.selected;
            }
            if (selected.length) {
                this.sending = true;
                this.$inertia
                    .post(route("admin.videos.approve"), {
                        videos: selected
                    })
                    .then(() => {
                        this.sending = false;
                    });
            }
        },
        rejectVideos: function(id) {
            let selected = [];
            if (id) selected.push(id);
            else {
                selected = this.selected;
            }
            if (selected.length) {
                this.sending = true;
                this.$inertia
                    .post(route("admin.videos.reject"), { videos: selected })
                    .then(() => {
                        this.sending = false;
                    });
            }
        }
    }
};
</script>
