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
import { FQDN } from '@/config/config.constants';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { UnauthorizedViewExceptionFilter } from '@/common/filters/unauthorized-view-exception.filter';
import { JwtOrUnrestrictedAuth } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { UnconfiguredSpotAuth } from '@/auth/unconfigured-spot/unconfigured-spot-auth.decorator';
import { Spot } from '@/spots/types/spot';
import { User } from '@/users/types/user';
import { Response } from 'express';
import { ConfiguredSpotViewExceptionFilter } from '@/spots/filters/configured-spot-view-exception.filter';

@ApiTags('Views')
@Controller('spots')
@UseFilters(UnauthorizedViewExceptionFilter)
export class SpotsViewsController {
	constructor(
		private readonly spotsService: SpotsService,
		private readonly configService: ConfigService,
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
	@Render('spots/form')
	async renderCreateSpot(
		@AuthUser() user: User,
	): Promise<Record<string, string>> {
		return {
			title: 'Create a new spot | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
		};
	}

	@Get('latest')
	@JwtOrUnrestrictedAuth()
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
		@AuthUser() user: User | undefined,
	): Promise<Record<string, string | undefined | Spot[]>> {
		const spots = await this.spotsService.getPublicSpots();

		return {
			title: 'Latest spots | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
			spots,
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
	@Render('spots/list')
	async renderSpotsList(
		@AuthUser() user: User,
	): Promise<Record<string, string | Spot[]>> {
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
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<{ title: string; spot: Spot; toConfigure: boolean }> {
		const spot = await this.spotsService.getSpot(id, user);

		return {
			title: 'Configure the spot | Spot in',
			spot,
			toConfigure: true,
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
	@Render('spots/form')
	async renderEditSpot(
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<Record<string, string | Spot>> {
		const spot = await this.spotsService.getSpot(id, user);

		return {
			title: 'Edit the spot | Spot in',
			username: user.username,
			email: user.email,
			role: user.role,
			spot,
		};
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

		return res.render('spots/redirect', {
			title: 'Redirecting | Spot in',
			spot: spotWithUser,
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
	): Promise<Record<string, string | undefined | Spot> | void> {
		const spot = await this.spotsService.getSpot(id);
		const fqdn = this.configService.get(FQDN, { infer: true });
		const redirection = `${fqdn}/spots/${spot.id}/redirect`;

		try {
			const qrcodeSvg = await qrcode.toString(redirection, { type: 'svg' });

			return {
				username: user?.username,
				email: user?.email,
				role: user?.role,
				title: 'Spot',
				spot,
				qrcode: qrcodeSvg,
			};
		} catch (error) {
			console.error(error);
		}
	}
}
