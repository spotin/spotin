import * as markdownit from 'markdown-it';
import * as markdownItAttrs from 'markdown-it-attrs';
import { Injectable } from '@nestjs/common';

@Injectable()
export class MarkdownService {
	private readonly md: markdownit;

	constructor() {
		this.md = markdownit({
			typographer: false,
			linkify: true,
		});

		// eslint-disable-next-line @typescript-eslint/no-unsafe-argument
		this.md.use(markdownItAttrs, {
			allowedAttributes: ['class', 'style'],
		});

		// Customize heading rendering to shift all headings down by one level.
		// This allows to keep the h1 for the spot title
		this.md.renderer.rules.heading_open = (
			tokens,
			idx,
			options,
			env,
			renderer,
		): string => {
			const token = tokens[idx];

			// Extract number from h1, h2, etc.
			const level = parseInt(token.tag.slice(1));

			// Shift down by 1, max h6
			const newLevel = Math.min(level + 1, 6);

			return `<h${newLevel}${renderer.renderAttrs(token)}>`;
		};

		this.md.renderer.rules.heading_close = (tokens, idx): string => {
			const token = tokens[idx];

			const level = parseInt(token.tag.slice(1));

			const newLevel = Math.min(level + 1, 6);

			return `</h${newLevel}>`;
		};
	}

	public renderMarkdown(string: string): string {
		return this.md.render(string);
	}
}
