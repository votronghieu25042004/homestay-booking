/// <reference types="vite/client" />
declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}
import { route as ziggyRoute } from 'ziggy-js';

declare global {
    var route: typeof ziggyRoute;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
    }
}