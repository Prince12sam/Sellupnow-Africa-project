<?php


namespace plugins\PageBuilder\Traits;


trait RenderViewString
{
    public function viewPath(){
        return 'pagebuilder::';
    }

    public function renderBlade($blade_name, $data = [], $moduleName = '') : string
    {
        try {
            // Define the path to the JSON file
            $modulesStatusPath = base_path('modules_statuses.json');

            // Check if the file exists
            if (!file_exists($modulesStatusPath)) {
                throw new \Exception("Modules status file not found at {$modulesStatusPath}");
            }

            // Read the file contents
            $modulesStatusContent = file_get_contents($modulesStatusPath);

            // Decode the JSON content
            $modulesStatus = json_decode($modulesStatusContent, false);

            // Check for JSON errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error decoding JSON: " . json_last_error_msg());
            }

            // If JSON content is null or false, return an empty string
            if ($modulesStatus === null || $modulesStatus === false) {
                return '';
            }

            // Check if the module is disabled
            if (!empty($moduleName)) {
                $moduleStatusKey = ucfirst(strtolower($moduleName));
                if (property_exists($modulesStatus, $moduleStatusKey) && $modulesStatus->$moduleStatusKey === false) {
                    return ''; // Return an empty string if the module is disabled
                }
            }

            // Determine the view path
            $view_path = !empty($moduleName) ? strtolower($moduleName) . '::addon-view.' : $this->viewPath();

            // Merge additional settings into the data array
            $data = array_merge($data, ['settings' => $this->get_settings()]);

            // Render and return the Blade template
            return view($view_path . $blade_name, $data)->render();
        } catch (\Exception $e) {
            // Log the error and return an empty string
            \Log::error($e->getMessage());
            return '';
        }
    }
}
