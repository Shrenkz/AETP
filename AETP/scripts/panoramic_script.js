let scene, camera, renderer, controls, raycaster, mouse, houseModel;

    function init() {
      scene = new THREE.Scene();
      scene.background = new THREE.Color(0x87ceeb);

      camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
      camera.position.set(0, 5, 5);

      renderer = new THREE.WebGLRenderer({ antialias: true });
      renderer.setSize(window.innerWidth, window.innerHeight);
      document.getElementById('scene-container').appendChild(renderer.domElement);

      const grassTexture = new THREE.TextureLoader().load('Green_Grass.jpg');
      grassTexture.wrapS = THREE.RepeatWrapping;
      grassTexture.wrapT = THREE.RepeatWrapping;
      grassTexture.repeat.set(50, 50);
      const groundMaterial = new THREE.MeshStandardMaterial({ map: grassTexture });
      const groundGeometry = new THREE.PlaneGeometry(1000, 1000);
      const ground = new THREE.Mesh(groundGeometry, groundMaterial);
      ground.rotation.x = -Math.PI / 2;
      ground.receiveShadow = true;
      scene.add(ground);

      const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
      scene.add(ambientLight);

      const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
      directionalLight.position.set(10, 20, 10);
      directionalLight.castShadow = true;
      scene.add(directionalLight);

      const hemiLight = new THREE.HemisphereLight(0xffffff, 0x444444, 0.5);
      hemiLight.position.set(0, 20, 0);
      scene.add(hemiLight);

      raycaster = new THREE.Raycaster();
      mouse = new THREE.Vector2();

      controls = new THREE.OrbitControls(camera, renderer.domElement);
      controls.enableDamping = true;
      controls.dampingFactor = 0.05;
      controls.screenSpacePanning = false;
      controls.minDistance = 1;
      controls.maxDistance = 50;
      controls.maxPolarAngle = Math.PI / 2.1;

      const loader = new THREE.GLTFLoader();
      loader.load('try.gltf', function (gltf) {
        houseModel = gltf.scene;
        houseModel.scale.set(0.1, 0.1, 0.1);
        houseModel.position.set(0, 0, 0);

        houseModel.traverse((node) => {
          if (node.isMesh) {
            node.material.side = THREE.DoubleSide;
            node.castShadow = true;

            if (!node.name || node.name === "") {
              node.name = `Object-${Math.random().toString(36).substr(2, 9)}`;
            }
            console.log(`Node Name: ${node.name}, ID: ${node.uuid}`);
          }
        });

        scene.add(houseModel);
        animate();
      }, undefined, function (error) {
        console.error('An error occurred while loading the model:', error);
      });

      window.addEventListener('resize', onWindowResize, false);
      renderer.domElement.addEventListener('click', onMouseClick, false);
    }

    function onWindowResize() {
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(window.innerWidth, window.innerHeight);
    }

    function animate() {
      requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, camera);
    }

    function onMouseClick(event) {
      event.preventDefault();

      mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
      mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

      raycaster.setFromCamera(mouse, camera);

      const intersects = raycaster.intersectObject(houseModel, true);

      if (intersects.length > 0) {
        const clickedObject = intersects[0].object;
        let title, description;

        switch (clickedObject.name) {
          case 'gate':
          case 'gate1':
          case 'gate2':
            title = 'AETP Gate';

            description = 'This is a traditional gate located in the AETP.';
            break;
          case 'main-office':
          case 'main-office1':
          case 'main-office2':
            title = 'Main Office';
            description = 'This is the main office of the AETP.';
            break;
          case 'wash-area':
            title = 'Wash Area';
            description = 'You can wash your hands and face here.';
            break;
          default:
            title = 'Unknown Object';
            description = `Object name: ${clickedObject.name}. This is an object with no additional information.`;
            break;
        }

        showModal(title, description);
      }
    }

    function showModal(title, description) {
      document.getElementById('modalTitle').textContent = title;
      document.getElementById('modalDescription').textContent = description;
      document.getElementById('modal').style.display = 'block';
    }

    init();

    // Get all navbar items
    const navItems = document.querySelectorAll('.nav-item');

    // Add click event listener to each nav item
    navItems.forEach(item => {
      item.addEventListener('click', function (e) {
        e.preventDefault();
        // Remove active class from all items
        navItems.forEach(navItem => navItem.classList.remove('active'));
        // Add active class to clicked item
        this.classList.add('active');
      });
    });

    document.getElementById('closeModal').addEventListener('click', function () {
      document.getElementById('modal').style.display = 'none';
    });

    // Touch controls for mobile
    let touchStartX = 0;
    let touchStartY = 0;
    let touchEndX = 0;
    let touchEndY = 0;

    renderer.domElement.addEventListener('touchstart', function (event) {
      touchStartX = event.touches[0].clientX;
      touchStartY = event.touches[0].clientY;
    }, false);

    renderer.domElement.addEventListener('touchmove', function (event) {
      touchEndX = event.touches[0].clientX;
      touchEndY = event.touches[0].clientY;
      handleTouch();
    }, false);

    function handleTouch() {
      const deltaX = touchEndX - touchStartX;
      const deltaY = touchEndY - touchStartY;

      // Adjust rotation speed based on touch movement
      controls.rotateLeft(deltaX * 0.002);
      controls.rotateUp(deltaY * 0.002);

      touchStartX = touchEndX;
      touchStartY = touchEndY;
    }