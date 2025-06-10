
  export function useGoogleMapApi(apiKey){
    return new Promise((resolve) => {
      const scriptId = 'map-api-script'
      const mapAlreadyAttached = !!document.getElementById(scriptId)
  
      if (mapAlreadyAttached) {
        if ((window).google) {
          resolve()
        }
      } else {
        ;(window).mapApiInitialized = resolve
  
        const script = document.createElement('script')
        script.id = scriptId
        script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&loading=async&callback=mapApiInitialized`
        document.body.appendChild(script)
      }
    })
  }
  