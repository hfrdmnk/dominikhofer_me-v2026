(function(){"use strict";panel.plugin("dominik/bluesky",{sections:{bluesky:{data(){return{isLoading:!1,label:"",lastSync:null,postCount:0}},computed:{formattedLastSync(){return this.lastSync?new Date(this.lastSync).toLocaleString():"Never"}},async created(){const t=await this.load();this.label=t.label,this.lastSync=t.lastSync,this.postCount=t.postCount},methods:{async sync(){this.isLoading=!0;try{await this.$api.post("bluesky/sync"),this.$panel.notification.success("Bluesky posts synced successfully");const t=await this.load();this.lastSync=t.lastSync,this.postCount=t.postCount}catch(t){this.$panel.notification.error(t.message||"Failed to sync")}finally{this.isLoading=!1}}},template:`
        <k-section :label="label">
          <k-box theme="info">
            <k-text>
              <p><strong>Cached posts:</strong> {{ postCount }}</p>
              <p><strong>Last sync:</strong> {{ formattedLastSync }}</p>
            </k-text>
          </k-box>
          <k-button
            icon="refresh"
            theme="positive"
            variant="filled"
            size="sm"
            :disabled="isLoading"
            @click="sync"
            style="margin-top: 0.75rem"
          >
            {{ isLoading ? "Syncing..." : "Sync Now" }}
          </k-button>
        </k-section>
      `}}})})();
