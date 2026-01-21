/**
 * Interactive Graph with D3.js
 * Sound similarity exploration
 */

import * as d3 from 'd3';

class ArbGraph {
    constructor(config) {
        this.config = {
            defaultDepth: 2,
            defaultMaxNodes: 50,
            apiEndpoint: '/wp-json/arborisis/v1/graph/explore',
            forceStrength: {
                charge: -300,
                link: 1,
                collide: 30,
            },
            nodeRadius: {
                min: 5,
                max: 20,
            },
            colors: {
                primary: '#16a34a',
                secondary: '#9333ea',
                edge: '#94a3b8',
            },
            ...config,
        };

        this.svg = null;
        this.simulation = null;
        this.currentSeed = null;
        this.data = { nodes: [], edges: [] };

        this.init();
    }

    init() {
        this.createSVG();
        this.createSimulation();

        // Hide loading
        setTimeout(() => {
            document.getElementById('graph-loading')?.classList.add('hidden');
        }, 500);
    }

    createSVG() {
        const svg = d3.select('#graph-svg');
        this.width = svg.node().clientWidth;
        this.height = svg.node().clientHeight;

        this.svg = svg;

        // Add zoom behavior
        const zoom = d3.zoom()
            .scaleExtent([0.1, 4])
            .on('zoom', (event) => {
                this.container.attr('transform', event.transform);
            });

        this.svg.call(zoom);

        // Create container for graph elements
        this.container = this.svg.append('g');

        // Create groups for edges and nodes
        this.edgesGroup = this.container.append('g').attr('class', 'edges');
        this.nodesGroup = this.container.append('g').attr('class', 'nodes');
    }

    createSimulation() {
        this.simulation = d3.forceSimulation()
            .force('charge', d3.forceManyBody().strength(this.config.forceStrength.charge))
            .force('link', d3.forceLink().id(d => d.id).strength(this.config.forceStrength.link))
            .force('collide', d3.forceCollide().radius(this.config.forceStrength.collide))
            .force('center', d3.forceCenter(this.width / 2, this.height / 2));
    }

    async explore(seedId, depth = this.config.defaultDepth, maxNodes = this.config.defaultMaxNodes) {
        this.currentSeed = seedId;

        try {
            const response = await fetch(
                `${this.config.apiEndpoint}?seed_id=${seedId}&depth=${depth}&max_nodes=${maxNodes}`
            );
            const data = await response.json();

            this.data = data;
            this.render();

            // Update stats
            document.getElementById('nodes-count').textContent = data.nodes.length;
            document.getElementById('edges-count').textContent = data.edges.length;
            document.getElementById('depth-level').textContent = depth;

        } catch (error) {
            console.error('Graph exploration failed:', error);
        }
    }

    render() {
        // Clear existing
        this.edgesGroup.selectAll('*').remove();
        this.nodesGroup.selectAll('*').remove();

        // Render edges
        const edges = this.edgesGroup.selectAll('line')
            .data(this.data.edges)
            .enter()
            .append('line')
            .attr('class', 'graph-edge')
            .attr('stroke-width', d => Math.max(1, d.weight / 2))
            .attr('stroke', this.config.colors.edge)
            .attr('stroke-opacity', 0.6);

        // Render nodes
        const nodes = this.nodesGroup.selectAll('g')
            .data(this.data.nodes)
            .enter()
            .append('g')
            .attr('class', 'graph-node')
            .call(this.drag());

        // Add circles
        nodes.append('circle')
            .attr('r', d => this.getNodeRadius(d))
            .attr('fill', d => d.id === this.currentSeed ? this.config.colors.primary : this.config.colors.secondary)
            .attr('stroke', '#fff')
            .attr('stroke-width', 2);

        // Add labels (for seed and important nodes)
        nodes.filter(d => d.id === this.currentSeed || d.plays > 100)
            .append('text')
            .text(d => d.title.length > 20 ? d.title.substring(0, 17) + '...' : d.title)
            .attr('font-size', '10px')
            .attr('dy', -15)
            .attr('text-anchor', 'middle')
            .attr('fill', 'currentColor')
            .attr('class', 'text-dark-700 dark:text-dark-300');

        // Add interactions
        nodes.on('click', (event, d) => {
            this.showSoundPanel(d);
        });

        nodes.on('mouseover', (event, d) => {
            this.showTooltip(event, d);
        });

        nodes.on('mouseout', () => {
            this.hideTooltip();
        });

        // Update simulation
        this.simulation
            .nodes(this.data.nodes)
            .on('tick', () => {
                edges
                    .attr('x1', d => d.source.x)
                    .attr('y1', d => d.source.y)
                    .attr('x2', d => d.target.x)
                    .attr('y2', d => d.target.y);

                nodes.attr('transform', d => `translate(${d.x},${d.y})`);
            });

        this.simulation.force('link').links(this.data.edges);
        this.simulation.alpha(1).restart();
    }

    getNodeRadius(node) {
        const { min, max } = this.config.nodeRadius;
        const plays = node.plays || 0;
        const maxPlays = Math.max(...this.data.nodes.map(n => n.plays || 0));
        const scale = maxPlays > 0 ? plays / maxPlays : 0;
        return min + (max - min) * scale;
    }

    drag() {
        return d3.drag()
            .on('start', (event, d) => {
                if (!event.active) this.simulation.alphaTarget(0.3).restart();
                d.fx = d.x;
                d.fy = d.y;
            })
            .on('drag', (event, d) => {
                d.fx = event.x;
                d.fy = event.y;
            })
            .on('end', (event, d) => {
                if (!event.active) this.simulation.alphaTarget(0);
                d.fx = null;
                d.fy = null;
            });
    }

    showSoundPanel(sound) {
        const panel = document.getElementById('sound-panel');
        const content = document.getElementById('panel-content');

        if (!panel || !content) return;

        content.innerHTML = `
            <h2 class="text-2xl font-bold mb-2">${sound.title}</h2>
            <div class="flex items-center gap-4 text-sm text-dark-600 dark:text-dark-400 mb-4">
                <span>${sound.plays || 0} écoutes</span>
                <span>•</span>
                <span>${sound.likes || 0} likes</span>
                ${sound.location_name ? `
                    <span>•</span>
                    <span>📍 ${sound.location_name}</span>
                ` : ''}
            </div>
            ${sound.tags && sound.tags.length ? `
                <div class="flex flex-wrap gap-2 mb-4">
                    ${sound.tags.map(tag => `<span class="badge badge-primary">${tag}</span>`).join('')}
                </div>
            ` : ''}
        `;

        // Set button actions
        document.getElementById('expand-node').onclick = () => {
            this.explore(sound.id);
            panel.classList.add('hidden');
        };

        document.getElementById('view-sound').href = `/sound/${sound.id}`;

        panel.classList.remove('hidden');
    }

    showTooltip(event, sound) {
        const tooltip = document.getElementById('graph-tooltip');
        const content = document.getElementById('tooltip-content');

        if (!tooltip || !content) return;

        content.innerHTML = `
            <div class="font-bold">${sound.title}</div>
            <div class="text-xs opacity-80">${sound.plays || 0} plays • ${sound.likes || 0} likes</div>
        `;

        tooltip.style.left = event.pageX + 10 + 'px';
        tooltip.style.top = event.pageY + 10 + 'px';
        tooltip.classList.remove('hidden');
    }

    hideTooltip() {
        document.getElementById('graph-tooltip')?.classList.add('hidden');
    }

    async randomStart() {
        try {
            const response = await fetch('/wp-json/arborisis/v1/sounds?orderby=random&per_page=1');
            const sounds = await response.json();
            if (sounds.length > 0) {
                this.explore(sounds[0].id);
            }
        } catch (error) {
            console.error('Failed to get random sound:', error);
        }
    }

    async trendingStart() {
        try {
            const response = await fetch('/wp-json/arborisis/v1/sounds?orderby=trending&per_page=1');
            const sounds = await response.json();
            if (sounds.length > 0) {
                this.explore(sounds[0].id);
            }
        } catch (error) {
            console.error('Failed to get trending sound:', error);
        }
    }

    centerView() {
        const bounds = this.container.node().getBBox();
        const fullWidth = this.width;
        const fullHeight = this.height;
        const midX = bounds.x + bounds.width / 2;
        const midY = bounds.y + bounds.height / 2;

        const scale = 0.9 / Math.max(bounds.width / fullWidth, bounds.height / fullHeight);
        const translate = [fullWidth / 2 - scale * midX, fullHeight / 2 - scale * midY];

        this.svg.transition().duration(750).call(
            d3.zoom().transform,
            d3.zoomIdentity.translate(translate[0], translate[1]).scale(scale)
        );
    }

    reset() {
        this.currentSeed = null;
        this.data = { nodes: [], edges: [] };
        this.render();
        document.getElementById('nodes-count').textContent = '0';
        document.getElementById('edges-count').textContent = '0';
        document.getElementById('depth-level').textContent = '0';
    }

    exportPNG() {
        // Convert SVG to PNG and download
        const svgData = new XMLSerializer().serializeToString(this.svg.node());
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        canvas.width = this.width;
        canvas.height = this.height;

        img.onload = () => {
            ctx.drawImage(img, 0, 0);
            canvas.toBlob(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `arborisis-graph-${Date.now()}.png`;
                a.click();
                URL.revokeObjectURL(url);
            });
        };

        img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
    }
}

// Initialize graph when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('graph-svg')) {
        const config = window.graphConfig || {};
        window.arbGraph = new ArbGraph(config);
    }
});

export default ArbGraph;
