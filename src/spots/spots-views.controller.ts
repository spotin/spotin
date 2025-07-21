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
import { JwtAccessTokenAuth } from '@/auth/jwt/jwt-access-token-auth.decorator';
import { BASE_URL, EnvironmentVariables } from '@/config/config.constants';
import { AuthJwtPayload } from '@/auth/decorators/auth-jwt-payload.decorator';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { JwtAccessTokenOrUnrestrictedAuth } from '@/auth/jwt-access-token-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { UnconfiguredSpotAuth } from '@/auth/unconfigured-spot/unconfigured-spot-auth.decorator';
import { Spot } from '@/spots/types/spot';
import { Response } from 'express';
import { ConfiguredSpotViewExceptionFilter } from '@/spots/filters/configured-spot-view-exception.filter';
import { UserRole } from '@/users/enums/user-role';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { UsersService } from '@/users/users.service';

@ApiTags('Views')
@Controller('spots')
@UseFilters(UnauthorizedViewExceptionFilter)
export class SpotsViewsController {
	constructor(
		private readonly usersService: UsersService,
		private readonly spotsService: SpotsService,
		private readonly configService: ConfigService<EnvironmentVariables, true>,
	) {}

	@Get('create')
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Render the create a new spot page',
		description: 'Render the create a new spot page.',
		operationId: 'renderCreateSpot',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('spots/form')
	async renderCreateSpot(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string>> {
		const user = await this.usersService.getUser(payload.sub);

		return {
			title: 'Create a new spot | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get('latest')
	@JwtAccessTokenOrUnrestrictedAuth()
	@ApiOperation({
		summary: 'Render the list of latest public spots page',
		description: 'Render the list of latest public spots page.',
		operationId: 'renderLatestSpots',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('spots/latest')
	async renderLatestSpots(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string | undefined | Spot[]>> {
		const spots = await this.spotsService.getPublicSpots();

		if (payload?.sub) {
			const user = await this.usersService.getUser(payload.sub);

			return {
				title: 'Latest spots | Spot in',
				username: user.username,
				email: user.email,
				role: user.role,
				spots,
			};
		}

		return {
			title: 'Latest spots | Spot in',
			spots,
		};
	}

	@Get()
	@JwtAccessTokenAuth()
	@ApiOperation({
		summary: 'Render the spots page',
		description: 'Render the spots page.',
		operationId: 'renderSpotsList',
	})
	@ApiOkResponse({
		description: 'Render successful.',
	})
	@Render('spots/list')
	async renderSpotsList(
		@AuthJwtPayload() payload: JwtPayload,
	): Promise<Record<string, string | Spot[]>> {
		const user = await this.usersService.getUser(payload.sub);

		const spots = await this.spotsService.getSpots(user);

		return {
			title: 'Spots | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
			spots,
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
	@Render('spots/form')
	async renderConfigureSpot(
		@AuthJwtPayload() payload: JwtPayload,
		@Param('id') id: string,
	): Promise<Record<string, boolean | string | Spot>> {
		const user = await this.usersService.getUser(payload.sub);

		const spot = await this.spotsService.getSpot(id, user);

		return {
			title: 'Configure the spot | Spot in',
			spot,
			role: UserRole.GUEST,
		};
	}

	@Get(':id/delete')
	@JwtAccessTokenAuth()
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
		@AuthJwtPayload() payload: JwtPayload,
		@Param('id') id: string,
	): Promise<void> {
		const user = await this.usersService.getUser(payload.sub);

		await this.spotsService.deleteSpot(id, user);
	}

	@Get(':id/edit')
	@JwtAccessTokenAuth()
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
		@AuthJwtPayload() payload: JwtPayload,
		@Param('id') id: string,
		@Res() res: Response,
	): Promise<void> {
		try {
			const user = await this.usersService.getUser(payload.sub);

			const spot = await this.spotsService.getSpot(id, user);

			return res.render('spots/form', {
				title: 'Edit the spot | Spot in',
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
		const spotWithUser = await this.spotsService.getSpotWithUser(id);

		if (!spotWithUser.configured) {
			return res.redirect(`/spots/${id}/configure`);
		}

		if (
			spotWithUser.directAccessToWebsiteTarget &&
			spotWithUser.websiteTarget !== null
		) {
			return res.redirect(spotWithUser.websiteTarget);
		}

		return res.render('spots/redirect', {
			title: 'Redirecting | Spot in',
			spot: spotWithUser,
		});
	}

	@Get(':id')
	@JwtAccessTokenOrUnrestrictedAuth()
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
		@AuthJwtPayload() payload: JwtPayload,
		@Param('id') id: string,
	): Promise<Record<string, string | undefined | Spot> | void> {
		const spot = await this.spotsService.getSpot(id);
		const baseUrl = this.configService.get(BASE_URL, { infer: true });
		const redirection = `${baseUrl}/spots/${spot.id}/redirect`;

		try {
			const qrcodeSvg = await qrcode.toString(redirection, { type: 'svg' });

			if (payload?.sub) {
				const user = await this.usersService.getUser(payload.sub);

				return {
					title: 'Spot | Spot in',
					username: user.username,
					email: user.email,
					role: user.role,
					spot,
					qrcode: qrcodeSvg,
				};
			}

			return {
				title: 'Spot',
				spot,
				qrcode: qrcodeSvg,
			};
		} catch (error) {
			console.error(error);
		}
	}
}
