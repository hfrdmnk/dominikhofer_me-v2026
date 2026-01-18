panel.plugin("dominik/bluesky", {
  sections: {
    bluesky: {
      data() {
        return {
          isLoading: false,
          label: "",
          lastSync: null,
          postCount: 0
        };
      },
      computed: {
        formattedLastSync() {
          if (!this.lastSync) return "Never";
          return new Date(this.lastSync).toLocaleString();
        }
      },
      async created() {
        const response = await this.load();
        this.label = response.label;
        this.lastSync = response.lastSync;
        this.postCount = response.postCount;
      },
      methods: {
        async sync() {
          this.isLoading = true;
          try {
            await this.$api.post("bluesky/sync");
            this.$panel.notification.success("Bluesky posts synced successfully");
            // Reload section data
            const response = await this.load();
            this.lastSync = response.lastSync;
            this.postCount = response.postCount;
          } catch (error) {
            this.$panel.notification.error(error.message || "Failed to sync");
          } finally {
            this.isLoading = false;
          }
        }
      },
      template: `
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
      `
    }
  }
});
