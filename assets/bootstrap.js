import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();
app.debug = true;

console.log( '✅ Stimulus started with AssetMapper' );
export { app };