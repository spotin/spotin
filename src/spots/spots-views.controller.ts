import * as qrcode from 'qrcode';
import {
	Get,
	Controller,
	Render,
	Param,
	Res,
	UseFilters,
	Redirect,
} from '@nestjs/common';
import {
	ApiNotFoundResponse,
	ApiOkResponse,
	ApiOperation,
	ApiParam,
	ApiTags,
} from '@nestjs/swagger';
import { User } from '@prisma/client';
import { Response } from 'express';
import { ConfigService } from '@nestjs/config';
import { SpotsService } from '@/spots/spots.service';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { FQDN } from '@/config/config.constants';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { UnauthorizedMvcExceptionFilter } from '@/common/filters/unauthorized-mvc-exception.filter';
import { JwtOrUnrestrictedAuth } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.decorator';
import { UnconfiguredSpotAuth } from '@/auth/unconfigured-spot/unconfigured-spot-auth.decorator';

@ApiTags('Views')
@Controller('spots')
@UseFilters(UnauthorizedMvcExceptionFilter)
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
	renderCreateSpot(@AuthUser() user: User) {
		return {
			title: 'Create a new spot | Spot in',
			username: user?.username,
			email: user?.email,
			role: user?.role,
			action: `/spots`,
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
	async renderLatestSpots(@AuthUser() user: User | undefined) {
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
	async renderSpotsList(@AuthUser() user: User) {
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
	async renderConfigureSpot(@AuthUser() user: User, @Param('id') id: string) {
		const spot = await this.spotsService.getSpot(id, user);

		return {
			username: user?.username,
			email: user?.email,
			role: user?.role,
			spot,
			action: `/spots/${id}/configure`,
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
	async deleteSpot(@AuthUser() user: User, @Param('id') id: string) {
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
	) {
		try {
			const spot = await this.spotsService.getSpot(id, user);

			res.render('spots/form', {
				username: user?.username,
				email: user?.email,
				role: user?.role,
				spot,
				action: `/spots/${id}`,
			});
		} catch (error) {
			res.redirect(`/spots/${id}/configure`);
		}
	}

	@Get(':id/redirect')
	@ApiOperation({
		summary: 'Redirect to the link specified by the spot',
		description: 'Redirect to the link specified by the spot.',
		operationId: 'getSpotRedirection',
	})
	@ApiParam({
		name: 'id',
		description: 'The spot ID.',
		format: 'uuid',
	})
	@ApiOkResponse({
		description: 'Redirect successful.',
		type: ReadSpotDto,
	})
	@ApiNotFoundResponse({
		description: 'Spot has not been found.',
	})
	async getSpotRedirection(
		@Res({ passthrough: true }) res: Response,
		@Param('id') id: string,
	) {
		const spot = await this.spotsService.getSpot(id);

		if (!spot.configured) {
			res.redirect(`/spots/${id}/configure`);
		} else if (!spot.redirection) {
			res.redirect(`/spots/${id}`);
		} else {
			res.redirect(spot.redirection);
		}
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
	@ApiNotFoundResponse({
		description: 'Spot has not been found. Redirect to `/not-found` page.',
	})
	async renderSpot(
		@AuthUser() user: User | undefined,
		@Res() res: Response,
		@Param('id') id: string,
	) {
		const spot = await this.spotsService.getSpot(id);
		const fqdn = this.configService.get(FQDN, { infer: true });
		const redirection = `${fqdn}/spots/${spot.id}/redirect`;

		try {
			const qrcodeSvg = await qrcode.toString(redirection, { type: 'svg' });

			res.render('spots/view', {
				username: user?.username,
				email: user?.email,
				role: user?.role,
				title: 'Spot',
				spot,
				qrcode: qrcodeSvg,
			});
		} catch (error) {
			console.error(error);
		}
	}
}
