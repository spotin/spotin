import { Body, Controller, ForbiddenException, Param } from '@nestjs/common';
import { ApiTags } from '@nestjs/swagger';
import { UserRole } from '@/users/enums/user-role';
import { CreateSpotDto } from '@/spots/dtos/create-spot.dto';
import { UpdateSpotDto } from '@/spots/dtos/update-spot-type.dto';
import { SpotsService } from '@/spots/spots.service';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { CustomPatch } from '@/common/decorators/custom-patch.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { TokenOrJwtAuth } from '@/auth/token-or-jwt/token-or-jwt-auth.decorators';
import { User } from '@/users/types/user';
import { UnconfiguredSpotAuth } from '@/auth/unconfigured-spot/unconfigured-spot-auth.decorator';
import { ConfigureSpotDto } from '@/spots/dtos/configure-spot-type.dto';

@ApiTags('Spots')
@Controller('api/spots')
export class SpotsController {
	constructor(private readonly spotsService: SpotsService) {}

	@GetMany({
		path: 'public',
		name: 'Spots',
		summary: 'Get the public spots',
		operationId: 'getPublicSpots',
		responseType: [ReadSpotDto],
	})
	async getPublicSpots(): Promise<ReadSpotDto[]> {
		const spots = await this.spotsService.getPublicSpots();

		const spotsDto = spots.map(
			(spot) =>
				new ReadSpotDto({
					...spot,
					payload: spot.payload ? JSON.stringify(spot.payload) : null,
				}),
		);

		return spotsDto;
	}
	@CustomPatch({
		path: ':id/configure',
		name: 'Spot',
		summary: 'Configure the specified spot',
		bodyType: ConfigureSpotDto,
		responseType: ReadSpotDto,
		operationId: 'configureSpot',
	})
	@UnconfiguredSpotAuth()
	async configureSpot(
		@AuthUser() user: User,
		@Param('id') id: string,
		@Body() configureSpotDto: ConfigureSpotDto,
	): Promise<ReadSpotDto> {
		const configuredSpotDto = await this.spotsService.updateSpot(
			id,
			configureSpotDto,
			user,
		);

		return new ReadSpotDto({
			...configuredSpotDto,
			payload: configuredSpotDto.payload
				? configuredSpotDto.payload.toString()
				: null,
		});
	}

	@GetMany({
		name: 'Spots',
		summary: 'Get the spots',
		operationId: 'getSpots',
		responseType: [ReadSpotDto],
	})
	@TokenOrJwtAuth()
	async getSpots(@AuthUser() user: User): Promise<ReadSpotDto[]> {
		const spots = await this.spotsService.getSpots(user);

		const spotsDto = spots.map(
			(spot) =>
				new ReadSpotDto({
					...spot,
					payload: spot.payload ? JSON.stringify(spot.payload) : null,
				}),
		);

		return spotsDto;
	}

	@GetOne({
		name: 'Spot',
		summary: 'Get the specified spot',
		operationId: 'getSpot',
		responseType: ReadSpotDto,
	})
	async getSpot(@Param('id') id: string): Promise<ReadSpotDto> {
		const spot = await this.spotsService.getSpot(id);

		return new ReadSpotDto({
			...spot,
			payload: spot.payload ? JSON.stringify(spot.payload) : null,
		});
	}

	@CustomPost({
		name: 'Spot',
		summary: 'Create a new spot',
		bodyType: CreateSpotDto,
		responseType: ReadSpotDto,
		operationId: 'createSpot',
	})
	@TokenOrJwtAuth()
	async createSpot(
		@AuthUser() user: User,
		@Body() createSpotDto: CreateSpotDto,
	): Promise<ReadSpotDto> {
		if (createSpotDto.public) {
			const isCertifiedOrAdmin =
				user.role === UserRole.CERTIFIED_USER || user.role === UserRole.ADMIN;

			if (!isCertifiedOrAdmin) {
				throw new ForbiddenException(
					'Standard users cannot create public spots',
				);
			}
		}

		const newSpot = await this.spotsService.createSpot(createSpotDto, user);

		return new ReadSpotDto({
			...newSpot,
			payload: newSpot.payload ? JSON.stringify(newSpot.payload) : null,
		});
	}

	@CustomPatch({
		name: 'Spot',
		summary: 'Update the specified spot',
		bodyType: UpdateSpotDto,
		responseType: ReadSpotDto,
		operationId: 'updateSpot',
	})
	@TokenOrJwtAuth()
	async updateSpot(
		@AuthUser() user: User,
		@Param('id') id: string,
		@Body() updateSpot: UpdateSpotDto,
	): Promise<ReadSpotDto> {
		if (updateSpot.public) {
			const isCertifiedOrAdmin =
				user.role === UserRole.CERTIFIED_USER || user.role === UserRole.ADMIN;

			if (!isCertifiedOrAdmin) {
				throw new ForbiddenException(
					'Standard users cannot create public spots',
				);
			}
		}

		const updatedSpot = await this.spotsService.updateSpot(
			id,
			updateSpot,
			user,
		);

		return new ReadSpotDto({
			...updatedSpot,
			payload: updatedSpot.payload ? JSON.stringify(updatedSpot.payload) : null,
		});
	}

	@CustomDelete({
		name: 'Spot',
		summary: 'Delete the specified spot',
		operationId: 'deleteSpot',
	})
	@TokenOrJwtAuth()
	async deleteSpot(
		@AuthUser() user: User,
		@Param('id') id: string,
	): Promise<void> {
		await this.spotsService.deleteSpot(id, user);
	}
}
