import * as qrcode from 'qrcode';
import {
	Get,
	Controller,
	Render,
	Param,
	UseFilters,
	Redirect,
	Res,
} from '@nestjs/common';
import {
	ApiOkResponse,
	ApiOperation,
	ApiParam,
	ApiTags,
} from '@nestjs/swagger';
import { ConfigService } from '@nestjs/config';
import { SpotsService } from '@/spots/spots.service';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { BASE_URL, EnvironmentVariables } from '@/config/config.constants';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { JwtOrUnrestrictedAuth } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { UnconfiguredSpotAuth } from '@/auth/unconfigured-spot/unconfigured-spot-auth.decorator';
import { Spot } from '@/spots/types/spot';
import { User } from '@/users/types/user';
import { Response } from 'express';
import { ConfiguredSpotViewExceptionFilter } from '@/spots/filters/configured-spot-view-exception.filter';
import { UserRole } from '@/users/enums/user-role';
import { MarkdownService } from '@/markdown/markdown.service';

@ApiTags('Views')
@Controller('spots')
@UseFilters(UnauthorizedViewExceptionFilter)
export class SpotsViewsController {
	constructor(
		private readonly markdownService: MarkdownService,
		private readonly spotsService: SpotsService,
		private readonly configService: ConfigService<EnvironmentVariables, true>,
	) {}

	@Get('create')
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the create a new spot page',
		description: 'Render the create a new spot page.',
		operationId: 'renderCreateSpot',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('spots/create')
	renderCreateSpot(@AuthUser() user: User): Record<string, string> {
		return {
			title: 'ui.spots.create.title',
			description: 'ui.spots.create.description',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get()
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the spots page',
		description: 'Render the spots page.',
		operationId: 'renderSpotsList',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('spots/index')
	async renderSpotsList(
		@AuthUser() user: User,
	): Promise<Record<string, string | Spot[]>> {
		const spots = await this.spotsService.getSpots(user);

		const mdSpots = spots.map((spot) => {
			const mdDescription =
				spot.description &&
				this.markdownService.renderMarkdown(spot.description);

			return {
				...spot,
				description: mdDescription,
			};
		});

		return {
			title: 'ui.spots.index.title',
			description: 'ui.spots.index.description',
			username: user.username,
			email: user.email,
			role: user.role,
			spots: mdSpots,
		};
	}

	@Get(':id/configure')
	@UnconfiguredSpotAuth()
	@UseFilters(ConfiguredSpotViewExceptionFilter)
	@ApiOperation({
		summary: 'Render the configure a spot page',
		description: 'Render the configure a spot page.',
		operationId: 'renderConfigureSpot',
	})
	@ApiParam({
		name: 'id',
		description: 'The spot ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('spots/configure')
	async renderConfigureSpot(
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<Record<string, boolean | string | Spot>> {
		const spot = await this.spotsService.getSpot(id, user);

		return {
			title: 'ui.spots.configure.title',
			description: 'ui.spots.configure.description',
			spot,
			role: UserRole.GUEST,
		};
	}

	@Get(':id/delete')
	@JwtAuth()
	@ApiOperation({
		summary: 'Delete the specified spot',
		description: 'Delete the specified spot. Redirect to `/spots` page.',
		operationId: 'deleteSpot',
	})
	@ApiParam({
		name: 'id',
		description: 'The spot ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Redirect successful.',
	})
	@Redirect('/spots')
	async deleteSpot(
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<void> {
		await this.spotsService.deleteSpot(id, user);
	}

	@Get(':id/edit')
	@JwtAuth()
	@ApiOperation({
		summary: 'Render the edit a spot page',
		description: 'Render the edit a spot page.',
		operationId: 'renderEditSpot',
	})
	@ApiParam({
		name: 'id',
		description: 'The spot ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	async renderEditSpot(
		@AuthUser() user: User,
		@Param('id') id: string,
		@Res() res: Response,
	): Promise<void> {
		try {
			const spot = await this.spotsService.getSpot(id, user);

			return res.render('spots/edit', {
				title: 'ui.spots.edit.title',
				description: 'ui.spots.edit.description',
				username: user.username,
				email: user.email,
				role: user.role,
				spot,
			});
		} catch {
			res.redirect(`/spots`);
		}
	}

	@Get(':id/redirect')
	@ApiOperation({
		summary: 'Render the redirection page',
		description: 'Render the redirection page.',
		operationId: 'renderRedirectSpot',
	})
	@ApiParam({
		name: 'id',
		description: 'The spot ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	async getSpotRedirection(
		@Res() res: Response,
		@Param('id') id: string,
	): Promise<void> {
		const { description, ...spotWithUser } =
			await this.spotsService.getSpotWithUser(id);

		if (!spotWithUser.configured) {
			return res.redirect(`/spots/${id}/configure`);
		}

		if (
			spotWithUser.directAccessToWebsiteTarget &&
			spotWithUser.websiteTarget !== null
		) {
			return res.redirect(spotWithUser.websiteTarget);
		}

		const mdDescription =
			description && this.markdownService.renderMarkdown(description);

		return res.render('spots/redirect', {
			title: 'ui.spots.redirect.title',
			description: 'ui.spots.redirect.description',
			spot: {
				...spotWithUser,
				description: mdDescription,
			},
		});
	}

	@Get(':id')
	@JwtOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the specified spot page',
		description: 'Render the specified spot page.',
		operationId: 'renderSpot',
	})
	@ApiParam({
		name: 'id',
		description: 'The spot ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('spots/view')
	async renderSpot(
		@AuthUser() user: User | undefined,
		@Param('id') id: string,
		@Res() res: Response,
	): Promise<Record<string, string | undefined | Spot> | void> {
		try {
			const { description, ...spot } = await this.spotsService.getSpot(
				id,
				user,
			);

			const mdDescription =
				description && this.markdownService.renderMarkdown(description);

			const baseUrl = this.configService.get(BASE_URL, { infer: true });
			const redirection = `${baseUrl}/spots/${spot.id}/redirect`;

			try {
				const qrcodeSvg = await qrcode.toString(redirection, {
					errorCorrectionLevel: 'high',
					type: 'svg',
				});

				return {
					title: 'ui.spots.view.title',
					description: 'ui.spots.view.description',
					username: user?.username,
					email: user?.email,
					role: user?.role,
					spot: {
						...spot,
						description: mdDescription,
					},
					qrcode: qrcodeSvg,
				};
			} catch (error) {
				console.error(error);
			}
		} catch {
			res.redirect(`/spots/${id}/redirect`);
		}
	}
}
