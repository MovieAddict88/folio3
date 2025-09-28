document.addEventListener('DOMContentLoaded', () => {
    const mainContent = document.getElementById('main-content');
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.portfolio-section');
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const themeToggles = document.querySelectorAll('.theme-toggle');

    // --- Data Fetching and Rendering ---
    const fetchData = async () => {
        try {
            const response = await fetch('api/get_data.php');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            renderContent(data);
        } catch (error) {
            console.error('Failed to fetch data:', error);
            mainContent.innerHTML = '<p>Error loading content. Please try again later.</p>';
        }
    };

    const renderContent = (data) => {
        // Update titles and profile pic
        document.querySelector('.portfolio-title-desktop').textContent = data.settings.site_title;
        document.querySelector('.portfolio-title-mobile').textContent = data.settings.site_title;
        document.querySelector('.profile-pic').src = `assets/images/${data.settings.profile_pic}`;

        // Render each section
        renderHome(data.settings, data.projects);
        renderAbout(data.about);
        renderEducation(data.education);
        renderExperience(data.experience);
        renderSkills(data.skills);
        renderProjects(data.projects);
        renderTestimonials(data.testimonials);
        renderDownloads(data.downloads);
        renderContact(data.settings);
    };

    // --- Section Render Functions ---
    const renderHome = (settings, projects) => {
        const section = document.getElementById('home');
        section.innerHTML = `
            <h2 class="hero-name">${settings.site_title || 'Your Name'}</h2>
            <p class="hero-tagline">${settings.tagline || 'Your inspiring tagline'}</p>
            <div>
                <a href="${settings.cv_url || '#'}" class="cta-button">View Resume</a>
                <a href="#contact" class="cta-button nav-link">Contact Me</a>
            </div>
        `;
    };

    const renderAbout = (about) => {
        const section = document.getElementById('about');
        section.innerHTML = `
            <h2>About Me</h2>
            <p>${about.bio || ''}</p>
            <h3>My Philosophy</h3>
            <p>${about.philosophy || ''}</p>
            ${about.video_embed_url ? `<div class="video-container"><iframe src="${about.video_embed_url}" frameborder="0" allowfullscreen></iframe></div>` : ''}
        `;
    };

    const renderEducation = (education) => {
        const section = document.getElementById('education');
        let content = '<h2>Education</h2>';
        education.forEach(edu => {
            content += `
                <div class="entry">
                    <h4>${edu.degree} (${edu.year})</h4>
                    <p><em>${edu.institution}</em></p>
                    <p>${edu.description || ''}</p>
                </div>
            `;
        });
        section.innerHTML = content;
    };

    const renderExperience = (experience) => {
        const section = document.getElementById('experience');
        let content = '<h2>Experience</h2>';
        experience.forEach(exp => {
            content += `
                <div class="entry">
                    <h4>${exp.position} (${exp.year_range})</h4>
                    <p><em>${exp.institution}</em></p>
                    <p>${exp.description || ''}</p>
                </div>
            `;
        });
        section.innerHTML = content;
    };

    const renderSkills = (skills) => {
        const section = document.getElementById('skills');
        let hardSkills = '<h3>Hard Skills</h3><ul>';
        let softSkills = '<h3>Soft Skills</h3><ul>';
        skills.forEach(skill => {
            if(skill.type === 'hard') {
                hardSkills += `<li>${skill.name} - ${skill.level}%</li>`;
            } else {
                softSkills += `<li>${skill.name}</li>`;
            }
        });
        hardSkills += '</ul>';
        softSkills += '</ul>';
        section.innerHTML = `<h2>Skills</h2><div class="skills-container">${hardSkills}${softSkills}</div>`;
    };

    const renderProjects = (projects) => {
        const section = document.getElementById('projects');
        let content = '<h2>Projects</h2>';
        projects.forEach(proj => {
            content += `
                <div class="project-card">
                    <h4>${proj.title}</h4>
                    <p>${proj.description}</p>
                    <a href="${proj.external_link}" target="_blank">View Project</a>
                </div>
            `;
        });
        section.innerHTML = content;
    };

    const renderTestimonials = (testimonials) => {
        const section = document.getElementById('testimonials');
        let content = '<h2>Testimonials</h2>';
        testimonials.forEach(test => {
            content += `
                <blockquote class="testimonial">
                    <p>"${test.quote}"</p>
                    <footer>- ${test.author}, ${test.author_title}</footer>
                </blockquote>
            `;
        });
        section.innerHTML = content;
    };

    const renderDownloads = (downloads) => {
        const section = document.getElementById('downloads');
        let content = '<h2>Downloads</h2>';
        downloads.forEach(dl => {
            content += `<p><a href="${dl.file_path}" download>${dl.file_name}</a></p>`;
        });
        section.innerHTML = content;
    };

    const renderContact = (settings) => {
        const section = document.getElementById('contact');
        section.innerHTML = `
            <h2>Contact Me</h2>
            <p>Email me at: <a href="mailto:${settings.email}">${settings.email}</a></p>
            <form id="contact-form" class="contact-form">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <textarea name="message" placeholder="Your Message" rows="5" required></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
            <p id="form-status"></p>
        `;
        // Add form submission handler
        document.getElementById('contact-form').addEventListener('submit', handleContactForm);
    };


    // --- Navigation ---
    const handleNavigation = (e) => {
        e.preventDefault();
        const targetId = e.currentTarget.getAttribute('href');

        // Update active link
        navLinks.forEach(link => link.classList.remove('active'));
        e.currentTarget.classList.add('active');

        // Show target section
        sections.forEach(sec => {
            sec.classList.remove('active');
            if (sec.id === targetId.substring(1)) {
                sec.classList.add('active');
            }
        });

        // Close mobile menu on navigation
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
        }
    };

    navLinks.forEach(link => link.addEventListener('click', handleNavigation));

    // --- Mobile Menu Toggle ---
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // --- Theme Toggle ---
    const setInitialTheme = () => {
        const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', savedTheme);
    };

    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    });

    // --- Contact Form Submission ---
    const handleContactForm = async (e) => {
        e.preventDefault();
        const form = e.target;
        const statusEl = document.getElementById('form-status');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        statusEl.textContent = 'Sending...';

        try {
            const response = await fetch('api/contact.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (response.ok) {
                statusEl.textContent = result.message;
                form.reset();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            statusEl.textContent = `Error: ${error.message}`;
        }
    };


    // --- Initializations ---
    fetchData();
    setInitialTheme();
});